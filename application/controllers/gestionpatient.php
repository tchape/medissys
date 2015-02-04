<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class GestionPatient extends CI_Controller{

	protected $count = 0;
	protected $cnt = 0;

	public $resInsert;

	public function __construct(){

		parent::__construct();

		$this->sess_user();

		$this->load->helper('assets');
		$this->load->library('layout');

		$this->load->library('errors'); //TODO: Class à développer pour rendre les variables portables dans les vues;

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error">','</p>');

		$this->layout->ajouter_css('layout');

		$this->layout->getId($this->session->userdata('nom'));

		$this->load->model('Gestionpatient_model');

		$this->load->library('javascript', array('js_library_driver' => 'jquery', 'autoload' => FALSE));

	}


	public function sess_user(){

		if ( ! $this->session->userdata('nom') ) {

			//redirect('Idenfication/index','refresh');
			redirect();
		}
	}

	public function acceuil(){

		$this->layout->view('patient/gestionpatient');

	}

	public function rechercherDossier(){

		$numdossier = $this->input->post('numerodossier');

		$config = array(
					array(
						'field' => 'numerodossier',
						'label' => '',
						'rules' => 'trim|callback_check_numdossier|xss_clean'
					)
				  );

		$this->form_validation->set_rules($config);

		$this->form_validation->set_error_delimiters('<p class="error">','</p>');

		if ( $this->form_validation->run() == false ){

			$this->layout->view('patient/gestionpatient');

		}
		else {
			
			$result = $this->Gestionpatient_model->findDossier($numdossier);

			$resBoard = $this->Gestionpatient_model->getDataBoard($numdossier);

			if ( empty($result) ){

				$err = array('erreur' => DIR_NOT_FOUND);

				$this->layout->view('patient/rechercher_dossier_error',$err);

			}
			else{

				$i = 0;

				foreach ($resBoard as $key) {
					
					$data[$i] = $resBoard[$i];
					$i++; 
				}

				$array = array(
					'header' => $result[0],
					'board'  => $data
					);

				$this->layout->view('patient/consulter_dossier',$array);

			}
		}
	}

	public function modifierDossier(){

		//$this->layout->view('patient/modifierdossier');

		$num = $this->input->post('numerodossier');

		$config = array(
					array(
						'field' => 'numerodossier',
						'label' => '',
						'rules' => 'trim|callback_check_numdossier|xss_clean'
					)
				  );

		$this->form_validation->set_rules($config);

		$this->form_validation->set_error_delimiters('<p class="error">','</p>');

		if ( $this->form_validation->run() == false ){

			$this->layout->view('patient/modifierdossier');

		}
		else {

			$result = $this->Gestionpatient_model->findDossier($num);

			if ( empty($result) ){

				$err = array('erreur' => DIR_NOT_FOUND);

				$this->layout->view('patient/modifier_dossier_error',$err);

			}
			else{

				$this->layout->view('patient/consulter_dossier_modifier',$result[0]);

			}
		}
	}

	public function dossierModifier(){

		//echo $this->jquery->event('#submit',alert());
		//$num = $this->input->post('numerodossier');

		$alterValue = array('numdossier' => $this->input->post('numdossier'),
							'telephone' => $this->input->post('telephone'),
							'mail' => $this->input->post('email'),
							'adresse' => $this->input->post('adresse'),
							'profession' => $this->input->post('profession'),
							'observation' => $this->input->post('observation')
						);

		//Vérification des valeurs à enregistrer

		$config = array (
					array(
						'field' => 'email',
						'label' => '',
						'rules' => 'valid_email'
					)
				 );
		// Message d'erreur à afficher
		$this->form_validation->set_message('valid_email','veuillez entrer un email valide :: exemple: totoemail@email.fr');
		$this->form_validation->set_rules($config);


		if ( $this->form_validation->run() == false ){

			$result = $this->Gestionpatient_model->findDossier($alterValue['numdossier']);

			$this->layout->view('patient/consulter_dossier_modifier',$result[0]);

		}
		else{
			
			$resUpdate = $this->Gestionpatient_model->updateDossier($alterValue);

			if ( $resUpdate ){
				
				/* A developper */
				redirect('patient/modifier_dossier_success');

			}
		}
	}

	public function creerDossier(){

		$civilite = $this->loadCivilite();
		$date = $this->loadDateNaissance();

		$var = array('c' => $civilite, 'd' => $date);

		$this->layout->view('patient/creerdossier',$var);

		/* Récupération des données postées */
		$civilite = $this->input->post('civilite') + 1;
		$nom = $this->input->post('nom');
		$prenom = $this->input->post('prenom');

		/* Récupération date naissance */
		$jour = $this->input->post('jours') +1;
		$mois = $this->input->post('mois') +1;
		$annees = $this->input->post('annees') +1;


		$telephone = $this->input->post('telephone');
		$email = $this->input->post('email');
		$adresse = $this->input->post('adresse');
		$profession = $this->input->post('profession');
		$symp = $this->input->post('symptome');
		$obs = $this->input->post('diagnostic');

		/*  Attribution des rules aux champs */
		/** callback_require: rules redefinit pour les champs requis
		 ** gère la multiplication d'affichage de la même erreur */

		$config = array(
					array(
						'field' => 'nom',
						'label' => '',
						'rules' => 'trim|callback_check_required|xss_clean'
					),
					array(
						'field' => 'prenom',
						'label' => '',
						'rules' => 'trim|callback_check_required|xss_clean'
					),
					array(
						'field' => 'telephone',
						'label' => '',
						'rules' => 'trim|callback_check_required|xss_clean'
					),
					array(
						'field' => 'telephone',
						'label' => '',
						'rules' => 'callback_check_length'
					),
					array(
						'field' => 'email',
						'label' => '',
						'rules' => 'valid_email'
					),
					array(
						'field' => 'symptome',
						'label' => '',
						'rules' => 'trim|callback_check_required|xss_clean'
					)
			);

		$this->form_validation->set_message('valid_email', 'veuillez entrer un email valide :: exemple: totoemail@email.fr');

		$this->form_validation->set_rules($config);

		$this->form_validation->set_error_delimiters('<p class="error">','</p>');

		/* Fin de la customisation */

		if ( $this->form_validation->run() == false ){

			$this->load->view('patient/creerdossier_error');

		}
		else{

			// Database matching date;
			$fullDate = $this->Gestionpatient_model->matchDate($jour,$mois,$annees);

			$var = array ('nom' => $nom, 'prenom' => $prenom, 'date_naissance' => $fullDate, 'tel' => $telephone, 'mail' => $email, 'civilite' => $civilite,
						  'adresse' => $adresse, 'profession' => $profession, 'symptome' => $symp, 'observation' => $obs);
			
			$resInsert = $this->Gestionpatient_model->creerDossier($var);

			if ( $resInsert ){
				
				//$this->creerdossier_success($resInsert);
				redirect('GestionPatient/creerdossier_success');

			}
		}
	}

	public function creerdossier_success(){


		$this->layout->view('patient/creerdossier_success');

	}

	public function consulterDossier(){

		$res = $this->Gestionpatient_model->getLastInsertData();

		$this->layout->view('patient/consulter_dossier',$res[0]);

	}

	public function loadCivilite(){

		$civilite = $this->Gestionpatient_model->getCivilite();

		$i = 0;
	
		if ( !empty($civilite) ){

			foreach ($civilite as $index => $value) {

				$data['row'][$i] = array('libelle' => $civilite[$i]->libelle );

				$i++;

			}

			return $data;
		}
	}

	public function loadDateNaissance(){

		$i = 0;

		$jours = $this->Gestionpatient_model->getJours();
		$month = $this->Gestionpatient_model->getMois();
		$year = $this->Gestionpatient_model->getAnnees();

		/* Chargement de la liste de jours */

		foreach ($jours as $index) {

			$j[$i] = array('jours' => $jours[$i]->jours); 

			$i++;	
		}
		/* Fin chargement */

		$i = 0;

		/* Chargement de la liste de mois */

		foreach ($month as $index => $value) {
			
			$m[$i] = array('mois' => $month[$i]->mois);

			$i++;
		}
		/* Fin chargement */

		$i = 0;

		/* Chargement de la liste des années */

		foreach ($year as $index => $value) {
			
			$a[$i] = array('annee' => $year[$i]->annee);
			$i++;
		}
		/* Fin chargement */

		$date = array('array_jours' => $j, 'array_mois' => $m, 'array_annees' => $a);

		return $date;
	}

	public function check_required($str){

		if ( empty($str) && $this->count == 0 ){

			$this->form_validation->set_message('check_required','les champs contenant un (*) sont obligatoires.');

			$this->count++;

			return false;
		}
	}

	public function check_field($str){

		if ( empty($str) && $this->cnt == 0 ){

			$this->form_validation->set_message('check_field','Veuillez remplir les champs pour effectuer la recherche.');

			$this->cnt++;

			return false;
		}
	}

	public function check_numdossier($str){

		if ( empty($str) ){

			$this->form_validation->set_message('check_numdossier','Veuillez renseigner le numéro de dossier pour effectuer la recherche.');

			return false;
		}
	}


	public function check_numeric($str){


		if ( ! is_numeric($str) ){

			$this->form_validation->set_message('check_numeric','le numéro de téléphone doit être une valeur numérique');

			return false;
		}
	}

	public function check_length($str){

		$length = strlen($str);

		if ( ($length < LENGTH_MIN || $length > LENGTH_MAX) && !empty($str)){

			$this->form_validation->set_message('check_length','le numéro de téléphone doit comporter 8 caractères');

			return false;

		}
	}
}

?>