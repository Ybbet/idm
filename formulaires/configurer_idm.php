<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/utils');
include_spip('inc/config');

function formulaires_configurer_idm_charger_dist() {
	$idm_config = lire_config('idm');
	// Contexte du formulaire.
	$contexte = array(
		'source' => (_request('source') ? _request('source') : $idm_config['source']),
	);

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_configurer_idm_verifier_dist() {
	$erreurs = array();
	$source = _request('source');
	if (!empty($source)) {
		if (!preg_match(',IMG/$,', $source) or !preg_match(',^http,', $source)) {
			$erreurs['source'] = _T('idm:url_source_verif');
		}
		$adresse_site = lire_config('adresse_site');
		if ((!isset($erreurs['source']) or empty($erreurs['source']))
			and preg_match(',^' . $adresse_site . ',', $source)
		) {
			$erreurs['source'] = _T('idm:url_source_identique');
		}
	}

	return $erreurs;
}

function formulaires_configurer_idm_traiter_dist() {
	//Traitement du formulaire.
	$res['source'] = _request('source');
	// Donnée de retour.
	if (ecrire_config('idm', @serialize($res))) {
		$res['message_ok'] = _T('config_info_enregistree');
	} else {
		$res['message_erreur'] = _T('erreur_technique_enregistrement_impossible');
	}

	return $res;
}