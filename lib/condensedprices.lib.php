<?php
/* Copyright (C) 2025 Atu SuperAdmin <arthurl52100@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    condensedprices/lib/condensedprices.lib.php
 * \ingroup condensedprices
 * \brief   Library files with common functions for CondensedPrices
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function condensedpricesAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("condensedprices@condensedprices");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/condensedprices/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/condensedprices/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/condensedprices/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@condensedprices:/condensedprices/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@condensedprices:/condensedprices/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'condensedprices@condensedprices');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'condensedprices@condensedprices', 'remove');

	return $head;
}

function condensedprices_prepare_head()
{
	global $langs, $conf;

	$langs->load("condensedprices@condensedprices");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/condensedprices/salepricestab.php", 1);
	$head[$h][1] = $langs->trans("SalePricesTab");
	$head[$h][2] = 0;
	$h++;

	$head[$h][0] = dol_buildpath("/condensedprices/buypricestab.php", 1);
	$head[$h][1] = $langs->trans("BuyPricesTab");
	$head[$h][2] = 1;
	$h++;

	return $head;
}