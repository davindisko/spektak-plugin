<?php

namespace Davindisko\Spektak\Components;

use Cms\Classes\ComponentBase;
use Davindisko\Spektak\Models\Prestation;
use Davindisko\Spektak\Models\Settings;

class Dashboard extends ComponentBase {

    public function onChangePaiement() {
        $prestation = Prestation::findOrFail(post('id'));
        $prestation->paiement = post('val');
        $prestation->save();
    }

    // Récupère le nombre d'heures nécessaires dans les réglages du plugin
    public function nbHeuresNecessaires() {

        return Settings::get('nbh');

    }

    // Récupère l'allocation plancher dans les réglages du plugin
    public function allocPlancher() {

        return Settings::get('allocPlancher');

    }

    // Récupère la règlementation en vigueur
    public function reglementation() {

        return Settings::get('reglementation');

    }

    // Récupère la prochaine date d'échance pour calcul indépendamment de l'année saisie
    public function nextDateRenew() {

        $day = date('j', strtotime(Settings::get('date_renew')));
        $month = date('n', strtotime(Settings::get('date_renew')));

        $nextDateRenew = date("Y-m-d", mktime(0, 0, 0, $month, $day, date('Y')));
        
        if (strtotime($nextDateRenew) < strtotime('now')) {
            $nextDateRenew = date("Y-m-d", mktime(0, 0, 0, $month, $day, date('Y')+1));
        }
        return $nextDateRenew;
    }

    // Liste des prestations sur la période en cours
    public function prestationsPeriode() {
        $nextDateRenew = $this->nextDateRenew();

        $from = date('Y-m-d', strtotime($nextDateRenew . ' -1 year'));

        return Prestation::whereDate('date_start', '>=', $from)
            ->orderBy('date_start')
            ->get();
    }

    // Liste des prestations sur la période précédente
    public function prestationsPeriodePrec() {
        $nextDateRenew = $this->nextDateRenew();
        $from = date('Y-m-d', strtotime($nextDateRenew . ' -2 year'));
        $to = date('Y-m-d', strtotime($nextDateRenew . ' -1 year'));

        return Prestation::whereBetween('date_start', [$from, $to])
            ->orderBy('date_start')
            ->get();
    }

    // Calcul des salaires annuels (année calendaire)
    public function salaires() {
        $salaires = [];

        $from = date("Y-m-d", mktime(0, 0, 0, 1, 1, date('Y')));

        $salaires['anneeEnCours']['brut'] = Prestation::whereDate('date_start', '>=', $from)
            ->sum('salaire_brut');
        $salaires['anneeEnCours']['net'] = Prestation::whereDate('date_start', '>=', $from)
            ->sum('salaire_net');
        $salaires['anneeEnCours']['net_imposable'] = Prestation::whereDate('date_start', '>=', $from)
            ->sum('net_imposable');

        $from = date("Y-m-d", mktime(0, 0, 0, 1, 1, date('Y')-1));
        $to = date("Y-m-d", mktime(0, 0, 0, 12, 31, date('Y')-1));
        $salaires['anneePrec']['brut'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('salaire_brut');
        $salaires['anneePrec']['net'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('salaire_net');
        $salaires['anneePrec']['net_imposable'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('net_imposable');

        return $salaires;
    }

    // Calcul des salaires à venir (net)
    public function paiementAVenir() {

        $AVenir = [];
        
        $AVenir['net'] = Prestation::whereDate('date_start', '<=', date('Y-m-d'))
            ->where('paiement', false)
            ->sum('salaire_net');
        $AVenir['brut'] = Prestation::whereDate('date_start', '<=', date('Y-m-d'))
            ->where('paiement', false)
            ->sum('salaire_brut');
    
        return $AVenir;
    }

    // Calcul du nombre d'heures réalisées et à venir avant date de renouvellement
    public function heures() {

        $nbH = $this->nbHeuresNecessaires();
        $nextDateRenew = $this->nextDateRenew();
        $regl = $this->reglementation();

        $heures = [];        

        $from = date('Y-m-d', strtotime($nextDateRenew . ' -1 year'));
        $to = date('Y-m-d');
        
        $heures['realise']['nb_heures'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('nb_heures');
        $heures['realise']['nb_cachets'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('nb_cachets');
        $heures['realise']['total_heures'] = $heures['realise']['nb_cachets'] * 12 + $heures['realise']['nb_heures'];
        $heures['realise']['salaire_reference'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('salaire_brut');
        $heures['realise']['allocJournaliere'] = $this->allocJournaliere($heures['realise']['salaire_reference'], $heures['realise']['total_heures'], $regl);
        
        $to = date('Y-m-d', strtotime($nextDateRenew));
        $heures['previsionnel']['nb_heures'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('nb_heures');
        $heures['previsionnel']['nb_cachets'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('nb_cachets');
        $heures['previsionnel']['total_heures'] = $heures['previsionnel']['nb_cachets'] * 12 + $heures['previsionnel']['nb_heures'];
        $heures['previsionnel']['salaire_reference'] = Prestation::whereBetween('date_start', [$from, $to])
            ->sum('salaire_brut');
        $heures['previsionnel']['allocJournaliere'] = $this->allocJournaliere($heures['previsionnel']['salaire_reference'], $heures['previsionnel']['total_heures'], $regl);
        
        return $heures;

    }

    private function allocJournaliere(float $sr, int $nbh, int $regl) {

        switch ($regl) {
            case '2014': 
            return $this->allocJournaliere2014($sr, $nbh);
            break;

            case '2018':
            return $this->allocJournaliere2018($sr, $nbh);
            break;
        }
    }

    /*  CIRCULAIRE 2018
        Calcul de l'allocation journalière
        Minimum : 44 euros
        AJ = A + B + C
        A = AJ minimale x [0,36 x salaire de référence (SR - jusqu'à 13 700 €) + 0,05 x (SR – au-delà de 13 700 €)] / 5000
        B = AJ minimale x [0,26 x NHT (jusqu'à 690 heures) + 0,08 x (NHT – 690 heures)] / NH
        C = AJ minimale x 0,70
        Allocation journalière (AJ) minimale = 31,36 €
        NH : Nombre d'heures exigées sur la période de référence = 507 heures sur 365 ou 319 jours.
        NHT : Nombre d'heures travaillées.
    */
    private function allocJournaliere2018(float $sr, int $nht, float $aj = 31.36) {

        // Données
        $srPlafond = 13700;
        $nbhPlafond = 690;
        $allocPlancher = 44;
        $nh = 507;

        // Calcul de A
        $srplus = 0;
        if ($sr > $srPlafond) {
            $sr = $srPlafond;
            $srplus = $sr - $srPlafond;
        }
        $a = $aj * (0.36 * $sr + 0.05 * $srplus) / 5000;

        // Calcul de B
        $nhplus = 0;
        if ($nht > $nbhPlafond) {
            $nht = $nbhPlafond;
            $nhplus = $nht - $nbhPlafond;
        }
        $b = $aj * (0.26 * $nht + 0.08 * $nhplus) / $nh;

        // Calcul de C
        $c = $aj * 0.7;

        $alloc = $a + $b + $c;
        if ($alloc < $allocPlancher) $alloc = $allocPlancher;

        return $alloc;
    }


    /*  CONVENTION 2014
        Calcul de l'allocation journalière
        AJ = A + B + C
        A = AJ minimale23 x [0,40x SR24 (jusqu'à 12 000 €) + 0,05 x (SR4 - 12 000 €)] / NH25 x SMIC horaire26
        B = AJ minimale3 x [0,30 x NHT27 (jusqu'à 600 heures) + 0,10 x (NHT7 - 600 heures)] / NH6
        C = AJ minimale3 x 0,70
        Allocation journalière (AJ) minimale = 31,36 €
        NH : Nombre d'heures exigées sur la période de référence = 507 heures sur 365 ou 319 jours.
        NHT : Nombre d'heures travaillées.
    */
    private function allocJournaliere2014(float $sr, int $nht, float $aj = 31.36) {

        // Plafonds
        $srPlafond = 12000;
        $nbhPlafond = 600;
        //$allocPlancher = 0;
        $nh = 507;
        $smic = Settings::get('smic');

        // Calcul de A
        $srplus = 0;
        if ($sr > $srPlafond) {
            $sr = $srPlafond;
            $srplus = $sr - $srPlafond;
        }
        $a = ($aj * 0.4 * $sr + 0.05 * $srplus) / ($nh * $smic);

        // Calcul de B
        $nhplus = 0;
        if ($nht > $nbhPlafond) {
            $nht = $nbhPlafond;
            $nhplus = $nht - $nbhPlafond;
        }
        $b = ($aj * 0.3 * $nht + 0.1 * $nhplus) / $nh;

        // Calcul de C
        $c = $aj * 0.7;

        $alloc = $a + $b + $c;
        //if ($alloc < $allocPlancher) $alloc = $allocPlancher;

        return $alloc;
    }


    public function componentDetails() {
        return [
            'name' => 'Spektak Dashboard',
            'description' => 'Dashboard interface for Spektak',
        ];
    }


}
