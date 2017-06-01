<?php
/**
 * Générer le fichier bash de l'objet
 *
 * @plugin     Import des médias
 * @copyright  2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/IDM/Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_generate_bash_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (!empty($arg)) {
		include_spip('idm_fonctions');
		$result = false;
		if ($arg === 'spip_documents') {
			$result = idm_bash_medias();
		} elseif ($arg === 'spip') {
			$result = idm_bash_spip();
		} else {
			include_spip('base/objets');
			$result = idm_bash_objet(table_objet($arg));
		}

		return $result;
	}
}