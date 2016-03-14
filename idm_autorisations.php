<?php
/**
 * Définit les autorisations du plugin Importation de Médias
 *
 * @plugin     Importation de Médias
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\idm\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function idm_autoriser()
{

}


/**
 * Autorisation de voir `idm`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_idm_voir_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de configurer `idm`
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_idm_configurer_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 2
// ------
/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_idm
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurer_idm_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=idm
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_idm_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_idm
 * Pour le plugin Bando
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configurer_idm_bando_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=idm
 * Pour le plugin Bando
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_idm_bando_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

// ------
// Là, on va s'occuper des autorisations des liens vers les pages
// du plugin dans les menu, pour SPIP 3.
// cf. `menu` à la place de `bouton`
// ------
/**
 * Autorisation pour afficher le lien vers la page ?exec=configurer_idm
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_configureridm_menu_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation pour afficher le lien vers la page ?exec=idm
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_idm_menu_dist($faire, $type, $id, $qui, $opt)
{
	return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

?>
