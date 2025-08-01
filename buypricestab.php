<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       condensedprices/buypricestab.php
 *	\ingroup    condensedprices
 *	\brief      Home page of condensedprices top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once 'lib/condensedprices.lib.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

// Load translation files required by the page
$langs->loadLangs(array("condensedprices@condensedprices"));


// Page is only visible for users who can create and modify products
if ($user->hasRight('produit', 'creer')){
    
    // Actions
    $action = GETPOST('action', 'aZ09');

    if (GETPOST('supplierSocid', 'alpha')){
        $supplierSocid = GETPOST('supplierSocid', 'alpha');
    } else {
        $supplierSocid = '';
    }
    
    // Variables to define the limits of the request and the number of rows printed
    $limit = GETPOSTINT('limit') ? GETPOSTINT('limit') : $conf->liste_limit;
    $page = GETPOSTINT('pageplusone') ? (GETPOSTINT('pageplusone') - 1) : GETPOSTINT("page");
    
    
    
    // SQL request
    
    if ($supplierSocid == ''){
        $sql = '';
        $num = 0;
    } else {
        $sql = 'SELECT fk_product as prod_id from llx_product_fournisseur_price as pfp WHERE fk_soc = '.$supplierSocid;
        $resql = $db->query($sql);
    
        $num = $db->num_rows($resql);
    }


    $soc = new Societe($db);
    $prodFourn = new ProductFournisseur($db);
    $prod = new Product($db);

    /* Content of the page */

    llxHeader("", $langs->trans("CondensedPricesArea"), '', '', 0, 0, '', '', '', 'mod-condensedprices page-index');

    print load_fiche_titre($langs->trans("CondensedPricesArea"), '', 'condensedprices.png@condensedprices');

    $head = condensedprices_prepare_head();

    dol_fiche_head($head, $active = 1);

    /* Filters */
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';

    // Box to choose a thirdparty to copy the price list
	print $langs->trans('ThirdParty').' '.img_picto('', 'company', 'class="pictofixedwidth"').$form->select_company($supplierSocid, 'supplierSocid', '((s.fournisseur:=:1) AND (s.status:=:1))', 'SelectThirdParty', 1, 0, null, 0, 'minwidth175 maxwidth300 widthcentpercentminusxx');
	print '<input type="submit" class="button buttonform small" value="'.$langs->trans("UPDATE").'">';
	print '<br>';

    print '</form>';
    print '<br>';

    // List of the editable products
    print '<table class="noborder centepercent">';
    print '<tr class="liste_titre">';
    print '<th>'.$langs->trans('Product').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('Label').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';    
    print '<th>'.$langs->trans('Supplier').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('SupplierRef').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('MinQty').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';    
    print '<th>'.$langs->trans('BuyingPrice').' '.$langs->trans('HT').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('BuyingPrice').' '.$langs->trans('TTC').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('Discount').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('SellingPrice').' '.$langs->trans('HT').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('SellingPrice').' '.$langs->trans('TTC').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('NewBuyingPrice').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '<th>'.$langs->trans('NewDiscount').($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th>';
    print '</tr>';

    $i = 0;
    while($i < $num){
        $obj = $db->fetch_object($resql);
        $prod->fetch($obj->prod_id);
        $prodFourn->fetch($obj->prod_id);
        $prodFourn->find_min_price_product_fournisseur($obj->prod_id);
        $prodFourn->fetch_product_fournisseur_price($prodFourn->product_fourn_price_id);
        $soc->fetch($supplierSocid);


        print '<tr class="oddeven">';
        print '<td class="nowrap">'.$prod->getNomUrl(1).'</td>';
        print '<td class="maxwidth100">'.$prod->label.'</td>';
        print '<td class="nowrap">'.$soc->getNomUrl(1).'</td>';
        print '<td class="nowrap">'.$prodFourn->fourn_ref.'</td>';
        print '<td class="nowrap">'.$prodFourn->fourn_qty.'</td>';
        print '<td class="nowrap">'.price2num($prodFourn->fourn_price).'</td>';
        print '<td class="nowrap">'.price2num($prodFourn->fourn_tva_tx).'</td>';
        print '<td class="nowrap">'.price2num($prodFourn->fourn_unitprice_with_discount).'</td>';
        print '<td class="nowrap">'.price2num($prod->price).'</td>';
        print '<td class="nowrap">'.price2num($prod->price_ttc).'</td>';
        print '<td><input type"text" id="newprice" name="newprice" value=""></td>';
        print '<td><input type"text" id="new_discount" name="new_discount" value=""></td>';
	


        print '</tr>';

        $i++;
    }    

    print '</table>';



    print '</div>';
}