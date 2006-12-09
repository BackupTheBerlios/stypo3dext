<?php
/**************************************************************
*
*  Copyright notice
*
*  (c) 2005 Guillaume Tessier<gtessier@dlcube.com> 
*  All rights reserved**  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Classe permattant la gestion d'objets GoogleMaps
 * @author	Vincent MAURY<vmaury@dlcube.com> 
 */

include_once("fonctions.php");

class GoogleMapsTool {

	var $GMKey="ABQIAAAAAP4NSDAKw6MQpKdVBzHNlhR1mHIi-zngvxfo0JRt-kxFKO-iExT1Rm3K-pfnq8S_Iddaj0vXNZb5qQ";
	var $MapWidth=650;	
	var $MapHeight=500;
	var $CMapLat=46.920255; // carte de France Affichée par défaut : c'est le centre
	var $CMapLong=2.373047;
	var $CMapZoom=6; // zoom défaut: carte de france
	var $CMapZoomAF=7; // Zoom qd adresse found
	var $IconImageFile="http://www.haras-nationaux.fr/GM_icons/chevalRougeRond.gif";
	var $IconImageSize=20;
	var $HomeImageFile="http://www.haras-nationaux.fr/GM_icons/homeRond.gif";
	var $outJSB=true; // booleen permettant disant si l'on sort le JS
	var $DataScript; // données en script
	var $tbresult=array();
	var $GeoCodeAddressData; // tableau des données d'une adresse géocodée, comme renvoyées par le sweb en csv
	
	function InitMap ($echDIWM=true) {
	
	$val2ret=($this->outJSB ? $this->OutGMJSWK : "");
	$val2ret.=($echDIWM ? '      
          <div id="map" style="width: '.$this->MapWidth.'px; height: '.$this->MapHeight.'px"></div>'
          : '');
          
	$val2ret.= ($this->outJSB ? '
          
          <script type="text/javascript">

    //<![CDATA[ ' : '');
    
	$val2ret.= '
	
	var map=null;
	var geocoder=null;
	var icon=null;
	
	function loadmap() {
		
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map"));
			map.setCenter(new GLatLng('.$this->CMapLat.','.$this->CMapLong.'), '.$this->CMapZoom.');
			map.addControl(new GLargeMapControl());
			map.addControl(new GMapTypeControl());
			map.addControl(new GOverviewMapControl());
			geocoder = new GClientGeocoder();
		
			
			// creation icone HN
			icon = new GIcon();
			icon.image = "'.$this->IconImageFile.'";
			icon.iconSize = new GSize('.$this->IconImageSize.', '.$this->IconImageSize.'); // !!! OBLIGATOIRE AVEC IE !!!
			icon.iconAnchor = new GPoint('.($this->IconImageSize /2).', '.($this->IconImageSize / 2).'); // ancrage de l ombre portee. IL FAUT renseigner sinon rien ne s affiche
			icon.infoWindowAnchor = new GPoint(1, 6); // position du depart de la bulle par rapport au coin haut gauche de l icone
			
			'.$this->DataScript.'
			/* on met les points ici, comme ça tout est dans le script ppal placé dans le header par typo
			sinon sous IE, ca déconne
			
			exemple en dur ...
			// centre de Dun
			var point = new GLatLng(46.318007,1.663055);
			var info =\'centre de <b>Dun le Palestel</b><br/>Promenade de la Foret<br/>23800 Dun-le-Palestel<br/>\';
			map.addOverlay(createMarker(point, info, icon)); 
			*/
		} else alert ("changez votre navigateur antediluvien pour afficher les cartes Google");
		
	} // fin fonction loadmap()
	
	function createMarker(point, info, icon) {
		var gmarkeroptions = new Object();
		gmarkeroptions.icon = icon;
		gmarkeroptions.clickable = true;
		gmarkeroptions.title = info;
		
		var marker = new GMarker(point,gmarkeroptions);
/*		GEvent.addListener(marker, "click", function() {
			//marker.openInfoWindowHtml(info);
			marker.openInfoWindow(info);
		});
*/		
		return marker;
	}
	';
// ces fonctions de geocodage en JS ne sont plus utilis�es
/* $val2ret.= '
	function showAddress(address) {
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
					alert("Impossible de localiser l\'adresse : \n" + address );
				} else {
					//alert(address + " found !");
					icon.image = "'.$this->HomeImageFile.'";
					map.addOverlay(createMarker(point,address,icon));
					map.setCenter(point, '.$this->CMapZoomAF.');
//					var marker = new GMarker(point);
//					map.addOverlay(marker);
//					marker.openInfoWindowHtml(address);
				}
			}
		);
	}
	
	function showAddress2(address) {
	 geocoder.getLocations(address, addAddressToMap);
	}

      function addAddressToMap(response) {
      map.clearOverlays();
      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
        marker = new GMarker(point);
        map.addOverlay(marker);
        marker.openInfoWindowHtml(place.address + \'<br>\' +
          \'<b>Country code:</b> \' + place.AddressDetails.Country.CountryNameCode + \'<br>\' +
          \'<b>AdministrativeAreaName:</b> \' + place.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName + \'<br>\' +
          \'<b>SubAdministrativeAreaName:</b> \' + place.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.SubAdministrativeAreaName
          );
      }
    } 
	 '; */
	
	$val2ret.= ($this->outJSB ? '
    //]]>
    </script>

<noscript>Javascript doit etre active.</noscript>
		' : '');
	return($val2ret);

	}
	
	function OutGMJSWK() {
	return('<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$this->GMKey.'"
      type="text/javascript"></script>');
      	}
      	
      	function OutMapIdS() {
      	return ('<script type="text/javascript">
			/*<![CDATA[*/
			<!--
			if (GBrowserIsCompatible()) {
			document.write(\'<div id="map" style="width: '.$this->MapWidth.'px; height: '.$this->MapHeight.'px"></div>\');
			setTimeout(\'loadmap()\', 500);
			}
			else {
			document.write(\'Javascript doit etre active pour pouvoir afficher les cartes Google\');
			}
			// -->
			/*]]>*/
		</script>');

      	}
/*
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!! la fonction de recherche du CT le plus proche est dans dlcube04_CAS/pi5/
!! elle est à implémenter ici !!

La doc du geocoder : http://www.google.com/apis/maps/documentation/#Geocoding_Structured

*/

// on passe un code région, cela va rechercher ses coordonnées sur la carte, et la recentre
	function RegionReCenter($region) {
		$rep_rg=db_qr_comprass("select geo_pos,geo_lat,geo_long from tx_dlcubehn03geomatic_points WHERE region='".$region."' AND type='CENT_REGIO' AND deleted=0 AND hidden=0");
		if ($rep_rg && $rep_rg[0]['geo_lat']>0) {
			$this->CMapLat=$rep_rg[0]['geo_lat'];
			$this->CMapLong=$rep_rg[0]['geo_long'];
			if ($rep_rg[0]['geo_pos']>0) $this->CMapZoom=$rep_rg[0]['geo_pos'];
			return($this->centerMap());	
		} else
			return(false);
	}
	
	function centerMap($lat=1000,$long=1000,$zoom=0) {
	return(($this->outJSB ? '
          <script type="text/javascript">

    //<![CDATA[ ' : '').'
		map.setCenter(new GLatLng('.($lat!=1000 ? $lat: $this->CMapLat).','.($long!=1000 ? $long :$this->CMapLong).'), '.($zoom>0 ? $zoom : $this->CMapZoom).');'
		.($this->outJSB ? '
    		//]]>
    	</script>' : ''));
	}
	
	
	function getPoints($param,$recenter=false,$limit=250) {
		$where=$this->retWhere($param);
		$rep=db_qr_comprass("select * from tx_dlcubehn03geomatic_points WHERE 1 $where AND deleted=0 AND hidden=0 order by cdpst limit $limit");
		if ($rep) {
			foreach ($rep as $lrep) {
				$this->tbresult[]=$this->cvlrep2res($lrep);
				$this->PointInfo=$lrep['name'];
				$ret.=$this->displayPoint($lrep['geo_lat'],$lrep['geo_long']);
			}
		} else $this->tbresult=false;
		return($ret);
	}
	
	function retWhere($param) {
		foreach ($param as $paire) {
			//print_r($paire);
			$key=$paire->key;
			$value=$paire->value;
			switch ($key) {
				case "type":
					$where.=" AND type ='$value' ";	
				break;
				
				case "typeCentre":
					$value=substr($value,2). // vire "ct" au début
					$where.=" AND type_det LIKE '%_$value_%' ";	
				
				break;
				
				case "centre":
					$where.=" AND name LIKE '%$value%' ";	
				
				break;
				
				case "codeRegion":
					$where.=" AND region ='$value' ";	
				break;
				
				case "number":
					$where.=" AND number =$value ";	
				break;
			}
		}
		return($where);
	}
	
	function displayPointbyNumber($number,$recenter=false) {
		$rep=db_qr_comprass("select geo_pos,geo_lat,geo_long,region,name from tx_dlcubehn03geomatic_points WHERE number=$number AND deleted=0 AND hidden=0");
		
		if ($this->PointInfo=="") $this->PointInfo=$rep[0]['name'];
		if ($rep && $rep[0]['geo_lat']>0) {
			if ($recenter) $ret=$this->RegionReCenter($rep[0]['region']);
			if (!$ret) $ret="";
			$ret.=$this->displayPoint ($rep[0]['geo_lat'],$rep[0]['geo_long']);
		}
		return($ret);
	}
	
	function look4closest($lat,$long,$limit=5,$type="%",$addwhere="",$pointinfo="") {
		$this->tbresult=array();
		// 111.64=2pi/360*6400, rayon de la terre, arrondi à 120 car dist à vol d'oiseau
		$rep=db_qr_comprass("select *,sqrt((geo_lat - $lat)*(geo_lat- $lat ) + (geo_long - $long)*(geo_long - $long))*120 as dist from tx_dlcubehn03geomatic_points WHERE type LIKE '$type' $addwhere  AND deleted=0 AND hidden=0 ORDER BY dist asc LIMIT $limit");
		if ($rep) {
			foreach ($rep as $lrep) {
				$this->tbresult[]=$this->cvlrep2res($lrep);
				$this->PointInfo=$lrep['name'];
				$ret.=$this->displayPoint($lrep['geo_lat'],$lrep['geo_long']);
			}
		} else $this->tbresult=false;
		// affiche le point demandé avec une pitite maison
		$this->PointInfo=$pointinfo;
		$ret.=$this->centerMap($lat,$long,8);
		$ret.=$this->displayPoint($lat,$long,$this->HomeImageFile);

		return($ret);
	}

	function displayPoint ($lat,$long,$img="") {
	return(($this->outJSB ? '
          <script type="text/javascript">

    //<![CDATA[ ' : '').'
		var point = new GLatLng('.$lat.",".$long.');
		var info =\''.addslashes($this->PointInfo).'\';
		icon.image = "'.($img !="" ? $img : $this->IconImageFile).'";
		map.addOverlay(createMarker(point, info, icon));'
		.($this->outJSB ? '
    		//]]>
    		</script>' : ''));
	}
	
	function map_geocoder($address) {
		$address = urlencode($address);
		$file = fopen('http://maps.google.com/maps/geo?q='.$address.'&key='.$this->GMKey.'&output=csv', "r");
       		while(!feof($file)) {
          	 	$data = $data . fgets($file, 4096);
       		}
       		fclose ($file);
       		$tbdata=explode(",",$data);
       		if ($tbdata[0]==200) {
       			$this->GeoCodeAddressData=$tbdata;
       			return(true);
       		}
		else return (false);

	}
	function cvlrep2res($lrep) {
		$ce["numPerso"]=$lrep['number'];
		$ce["nom"]=$lrep['name'];
		$ce["adresse1"]=$lrep["adresse1"];
		$ce["adresse2"]=$lrep["adresse2"];
		$ce["codePostal"]=$lrep["cdpst"];
		$ce["libelleCommune"]=$lrep["ville"];
		$ce["telephone"]=$lrep["tel"];
		$ce["telPortable"]=$lrep["cell"];
		$ce["telecopie"]=$lrep["fax"];
		$ce["mail"]=$lrep["mail"];
		$ce["dist"]=round($lrep['dist']);
		if (strstr($lrep["type_det"],"_mp_")) $ce['actCtmp']="O";
		if (strstr($lrep["type_det"],"_pr_")) $ce['actCtpr']="O";
		if (strstr($lrep["type_det"],"_tre_")) $ce['actCttre']="O";
		return($ce);
	}
			
}
/*if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"]);
}*/
?>
