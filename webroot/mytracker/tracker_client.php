<?php
	/*
	 * Module:	tracker_client.php
	 * Description: This is the module that checks the identify of the connecting clients.
	 *              This module will probably change frequently and so it is seperate from
	 *              the "main" tracker.php file.
	 *
	 * Author:	danomac
	 * Written:	28-May-2004
	 *
	 * Copyright (C) 2004 danomac
	 *
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 */
	require_once("funcsv2.php");
	require_once("config.php");


	/*
	 * This function checks to see if the client is Azureus, and if so,
	 * ensures they are not using an old version
	 */
	function checkAzureus($clientVer) {
			$allowedVersion[0] = 2;
			$allowedVersion[1] = 0;
			$allowedVersion[2] = 8;
			$allowedVersion[3] = 4;

		/*
		 * Darned Azureus changed the version string again 2005/03/09
	 	 */
		$azSplit = explode(";", $clientVer);
		$clientVer = $azSplit[0];

		if (stristr($clientVer, "Java"))
			showError("Azureus versions below ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2].".".$allowedVersion[3]." are not supported. Go to http://azureus.sf.net to upgrade.");	

		/*
		 * Format: 'Azureus 2.0.8.4'
		 */
		if (stristr($clientVer, "Azureus")) {
			//let's extract the version number
			$versionArray = explode(" ", $clientVer);

			//if there are two elements, this is an official Azureus version #
			if (count($versionArray) == 2) {
				$versionNumberArray = explode(".", $versionArray[1]);

				//if there are 4 elements to the version number, this is an official Azureus version #
				if (count($versionNumberArray) == 4) {
					//lets check the minor rev number to see if its a CVS
					if (!is_numeric($versionNumberArray[3])) {
						//if _CVS is in this particular element, it's still valid, extract it
						$position[0] = strpos($versionNumberArray[3], "_B", 1);
						$position[1] = strpos($versionNumberArray[3], "_CVS", 1);
						if ($position[0] === false && $position[1] === false)
							showError("Tracker code 0x07: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");
						else {
							//attempt to extract the number.
							if ($position[0] === false) {
								//assume the OLD _CVS tag

								$numericNumber = substr($versionNumberArray[3], 0, $position);
								if (is_numeric($numericNumber)) 
									$versionNumberArray[3]=$numericNumber;
								else
									showError("Tracker code 0x06: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");
							} else {
								//the new CVS tags
								$numericNumber = substr($versionNumberArray[3], 0, $position[0]);
								$cvsNumber = substr($versionNumberArray[3], $position[0]+2);
								if (is_numeric($numericNumber)) {
									$versionNumberArray[3]=$numericNumber;
									if (is_numeric($cvsNumber)) {
										if (!($cvsNumber >= 0 && $cvsNumber <= 999))
											showError("Tracker code 0x05: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");
									} else
										showError("Tracker code 0x04: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");
								} else
									showError("Tracker code 0x03: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");							}
						}

					}

					//check the version number, make sure it's allowed
					if ($versionNumberArray[0] == $allowedVersion[0]) {
						if ($versionNumberArray[1] == $allowedVersion[1]) {
							if ($versionNumberArray[2] == $allowedVersion[2]) {
								if ($versionNumberArray[3] == $allowedVersion[3]) {
									return true;
								} else 
									if ($versionNumberArray[3] < $allowedVersion[3]) {
										showError("This tracker only supports Azureus ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2].".".$allowedVersion[3]." and above. Go to http://azureus.sf.net to upgrade.");
									} else {
										return true;
									}
							} else 
								if ($versionNumberArray[2] < $allowedVersion[2]) {
									showError("This tracker only supports Azureus ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2].".".$allowedVersion[3]." and above. Go to http://azureus.sf.net to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[1] < $allowedVersion[1]) {
								showError("This tracker only supports Azureus ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2].".".$allowedVersion[3]." and above. Go to http://azureus.sf.net to upgrade.");
							} else {
								return true;
							}
					} else 
						if ($versionNumberArray[0] < $allowedVersion[0]) {
							showError("This tracker only supports Azureus ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2].".".$allowedVersion[3]." and above. Go to http://azureus.sf.net to upgrade.");
						} else {
							return true;
						}
				} else
					showError("Tracker code 0x02: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid Azureus client. See http://azureus.sf.net");	
		}
	}

	/*
	 * This function checks to see if the client is ABC, and if so,
	 * ensures they are not using an old version
	 */
	function checkABC($clientVer) {
			$allowedVersion[0] = 2;
			$allowedVersion[1] = 6;
			$allowedVersion[2] = 8;

		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 *
		 * First Az, now ABC? WTF? 2005/03/27
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else {
			if (($position = strpos($clientVer, "ABC")) !== false) {
				if ($position > 0) {
					$clientVer = substr($clientVer, $position);
				}
			}
		}

		/*
		 * Format: 'BitTorrent/ABC-2.6.8' or ABC x.x.x/ABC-3.0.0
		 */
		if (stristr($clientVer, "/ABC")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'BitTorrent' and 'ABC-x.x.x')
			 */
			if (count($versionArray) == 2) {
				/*
				 * Continue dismantling the version string (in this case, 
				 * there should be two elements: 'ABC' and 'x.x.x')
				 */
				$versionClientIDArray = explode("-", $versionArray[1]);

				if (count($versionClientIDArray) == 2) {
					$versionNumberArray = explode(".", $versionClientIDArray[1]);

					//if there are 3 elements to the version number, this is an official ABC version #
					if (count($versionNumberArray) == 3) {

						//check the version number, make sure it's allowed
						if ($versionNumberArray[0] == $allowedVersion[0]) {
							if ($versionNumberArray[1] == $allowedVersion[1]) {
								if ($versionNumberArray[2] == $allowedVersion[2]) {
									return true;
								} else 
									if ($versionNumberArray[2] < $allowedVersion[2]) {
										showError("This tracker only supports ABC ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://pingpong-abc.sf.net to upgrade.");
									} else {
										return true;
									}
							} else 
								if ($versionNumberArray[1] < $allowedVersion[1]) {
									showError("This tracker only supports ABC ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://pingpong-abc.sf.net to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[0] < $allowedVersion[0]) {
								showError("This tracker only supports ABC ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://pingpong-abc.sf.net to upgrade.");
							} else {
								return true;
							}
					} else
						showError("Tracker code 0x03: You don't appear to be using a valid ABC client. See http://pingpong-abc.sf.net");
				} else
					showError("Tracker code 0x02: You don't appear to be using a valid ABC client. See http://pingpong-abc.sf.net");	
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid ABC client. See http://pingpong-abc.sf.net");	
		}
	}

	/*
	 * This function checks to see if the client is Shadows old client, and if so,
	 * ensures they are not using a really old version
	 */
	function checkShadows($clientVer) {
		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTorrent/S-5.8.11'
		 */
		if (stristr($clientVer, "BitTorrent/S-")) {
				showError("This client is now depracated. Please upgrade to BitTornado. See http://www.bittornado.com");	
		}
	}

	/*
	 * This function checks to see if the client is a BitTornado client, and if so,
	 * ensures they are not using a really old version (up to 0.3.8)
	 */
	function checkBitTornado($clientVer) {
			$allowedVersion[0] = 0;
			$allowedVersion[1] = 2;
			$allowedVersion[2] = 0;

		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTorrent/T-0.2.0'
		 */
		if (stristr($clientVer, "BitTorrent/T-")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'BitTorrent' and 'T-x.x.x')
			 */
			if (count($versionArray) == 2) {
				/*
				 * Continue dismantling the version string (in this case, 
				 * there should be two elements: 'T' and 'x.x.x')
				 */
				$versionClientIDArray = explode("-", $versionArray[1]);

				if (count($versionClientIDArray) == 2) {
					$versionNumberArray = explode(".", $versionClientIDArray[1]);

					//if there are 3 elements to the version number, this is an official BitTornado version #
					if (count($versionNumberArray) == 3) {

						//check the version number, make sure it's allowed
						if ($versionNumberArray[0] == $allowedVersion[0]) {
							if ($versionNumberArray[1] == $allowedVersion[1]) {
								if ($versionNumberArray[2] == $allowedVersion[2]) {
									return true;
								} else 
									if ($versionNumberArray[2] < $allowedVersion[2] || $versionNumberArray[2] > 8) {
										showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
									} else {
										return true;
									}
							} else 
								if ($versionNumberArray[1] < $allowedVersion[1] || $versionNumberArray[1] > 3) {
									showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[0] < $allowedVersion[0] || $versionNumberArray[0] > 0) {
								showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
							} else {
								return true;
							}
					} else
						showError("Tracker code 0x03: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");
				} else
					showError("Tracker code 0x02: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");	
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");	

		}
	}

	/*
	 * This function checks to see if the client is a new BitTornado client, and if so,
	 * ensures they are not using a really old version ( >= 0.3.9)
	 */
	function checkNewBitTornado($clientVer) {
			$allowedVersion[0] = 0;
			$allowedVersion[1] = 3;
			$allowedVersion[2] = 9;

		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTornado")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTornado/T-0.3.9'
		 */
		if (stristr($clientVer, "BitTornado/T-")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'BitTornado' and 'T-x.x.x')
			 */
			if (count($versionArray) == 2) {
				/*
				 * Continue dismantling the version string (in this case, 
				 * there should be two elements: 'T' and 'x.x.x')
				 */
				$versionClientIDArray = explode("-", $versionArray[1]);

				if (count($versionClientIDArray) == 2) {
					$versionNumberArray = explode(".", $versionClientIDArray[1]);

					//if there are 3 elements to the version number, this is an official BitTornado version #
					if (count($versionNumberArray) == 3) {

						//check the version number, make sure it's allowed
						if ($versionNumberArray[0] == $allowedVersion[0]) {
							if ($versionNumberArray[1] == $allowedVersion[1]) {
								if ($versionNumberArray[2] == $allowedVersion[2]) {
									return true;
								} else 
									if ($versionNumberArray[2] < $allowedVersion[2]) {
										showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
									} else {
										return true;
									}
							} else 
								if ($versionNumberArray[1] < $allowedVersion[1]) {
									showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[0] < $allowedVersion[0]) {
								showError("This tracker only supports BitTornado ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittornado.com to upgrade.");
							} else {
								return true;
							}
					} else
						showError("Tracker code 0x03: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");
				} else
					showError("Tracker code 0x02: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");	
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid BitTornado client. See http://www.bittornado.com");	

		}
	}

	/*
	 * This function checks to see if the client is an original client, and if so,
	 * ensures they are not using a really old version
	 */
	function checkBitTorrent($clientVer) {
			$allowedVersion[0] = 3;
			$allowedVersion[1] = 4;
			$allowedVersion[2] = 2;

		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTorrent/3.4.2'
		 */
		if (stristr($clientVer, "BitTorrent/")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'BitTorrent' and 'x.x.x')
			 */
			if (count($versionArray) == 2) {
					$versionNumberArray = explode(".", $versionArray[1]);

					//if there are 3 elements to the version number, this is an official BitTornado version #
					if (count($versionNumberArray) == 3) {

						//check the version number, make sure it's allowed
						if ($versionNumberArray[0] == $allowedVersion[0]) {
							if ($versionNumberArray[1] == $allowedVersion[1]) {
								if ($versionNumberArray[2] == $allowedVersion[2]) {
									return true;
								} else 
									if ($versionNumberArray[2] < $allowedVersion[2]) {
										showError("This tracker only supports original BitTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittorrent.com to upgrade.");
									} else {
										return true;
									}
							} else 
								if ($versionNumberArray[1] < $allowedVersion[1]) {
									showError("This tracker only supports original BitTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittorrent.com to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[0] < $allowedVersion[0]) {
								showError("This tracker only supports original BitTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://www.bittorrent.com to upgrade.");
							} else {
								return true;
							}
					} else
						showError("Tracker code 0x03: You don't appear to be using a valid original BitTorrent client. See http://www.bittorrent.com");
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid original BitTorrent client. See http://www.bittorrent.com");	

		}
	}

	function checkforShyClients($clientVer, $ip, $iplong) {
		/*
		 * Some clients do not seem to send its own version string.
		 * All it sends is the python urllib string. So, if I client
		 * refuses to identify itself, let's ban it by IP address.
		 * Some of these can be used to abuse trackers.
		 *
		 * Only ban if IP banning is enabled/enforced.
		 */
		if ($GLOBALS["enable_ip_banning"] && !$GLOBALS["allow_unidentified_clients"] && $GLOBALS["filter_clients"]) {
			if (stristr($clientVer, "Python-urllib/")) {
				autoBanByIP($ip, $iplong, "Possible abusive/old client", $GLOBALS["autobanlength"]);
				showError("Possible abusive/old client - IP Banned");
			}
		}
	}

	/*
	 * This function checks to see if the client is BitSpirit
	 */
	function checkBitSpirit($clientVer) {
		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTorrent/BitSpirit' no version # reported.
		 */
		if (stristr($clientVer, "BitTorrent/BitSpirit")) {
			return true;
		}
	}

	/*
	 * This function checks to see if the client is a TurboBT client, and if so,
	 * ensures they are not using a really old version
	 */
	function checkTurboBT($clientVer) {
			$allowedVersion[0] = 5;
			$allowedVersion[1] = 0;

		/*
		 * Sometimes the python version is also in this string, and
		 * this causes this module to incorrectly deny the client a
		 * connection to the tracker. If there is something before the
		 * actual client ID string, remove it here first.
		 */
		if (($position = strpos($clientVer, "BitTorrent")) !== false) {
			if ($position > 0) {
				$clientVer = substr($clientVer, $position);
			}
		} else
			return false;

		/*
		 * Format: 'BitTorrent/TurboBT 5.0'
		 */
		if (stristr($clientVer, "BitTorrent/TurboBT")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'BitTorrent' and 'TurboBT x.x')
			 */
			if (count($versionArray) == 2) {
				/*
				 * Continue dismantling the version string (in this case, 
				 * there should be two elements: 'TurboBT' and 'x.x')
				 */
				$versionClientIDArray = explode(" ", $versionArray[1]);

				if (count($versionClientIDArray) == 2) {
					$versionNumberArray = explode(".", $versionClientIDArray[1]);

					//if there are 2 elements to the version number, this is an official TurboBT version #
					if (count($versionNumberArray) == 2) {

						//check the version number, make sure it's allowed
						if ($versionNumberArray[0] == $allowedVersion[0]) {
							if ($versionNumberArray[1] == $allowedVersion[1]) {
								return true;
							} else 
								if ($versionNumberArray[1] < $allowedVersion[1]) {
									showError("This tracker only supports TurboBT ".$allowedVersion[0].".".$allowedVersion[1]." and above. Go to http://sourceforge.net/projects/turbobt to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[0] < $allowedVersion[0]) {
								showError("This tracker only supports TurboBT ".$allowedVersion[0].".".$allowedVersion[1]." and above. Go to http://sourceforge.net/projects/turbobt to upgrade.");
							} else {
								return true;
							}
					} else
						showError("Tracker code 0x03: You don't appear to be using a valid TurboBT client. See http://sourceforge.net/projects/turbobt");
				} else
					showError("Tracker code 0x02: You don't appear to be using a valid TurboBT client. See http://sourceforge.net/projects/turbobt");	
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid TurboBT client. See http://sourceforge.net/projects/turbobt");	

		}
	}

	/*
	 * This function checks to see if the client is a rTorrent client, and if so,
	 * ensures they are not using a really old version.
	 */
	function checkLibtorrent($clientVer) {
			$allowedVersion[0] = 0;
			$allowedVersion[1] = 5;
			$allowedVersion[2] = 4;

		/*
		 * Format: 'libtorrent/0.5.4'
		 */
		if (stristr($clientVer, "libtorrent/")) {
			//let's extract the version number
			$versionArray = explode("/", $clientVer);

			/*
			 * Start dismantling the version string (in this case, 
			 * there should be two elements: 'libtorrent' and 'x.x.x')
			 */
			if (count($versionArray) == 2) {
				$versionNumberArray = explode(".", $versionArray[1]);

				//if there are 3 elements to the version number, this is an official libtorrent version #
				if (count($versionNumberArray) == 3) {

					//check the version number, make sure it's allowed
					if ($versionNumberArray[0] == $allowedVersion[0]) {
						if ($versionNumberArray[1] == $allowedVersion[1]) {
							if ($versionNumberArray[2] == $allowedVersion[2]) {
								return true;
							} else 
								if ($versionNumberArray[2] < $allowedVersion[2]) {
									showError("This tracker only supports rTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://libtorrent.rakshasa.no/ to upgrade.");
								} else {
									return true;
								}
						} else 
							if ($versionNumberArray[1] < $allowedVersion[1]) {
								showError("This tracker only supports rTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://libtorrent.rakshasa.no/ to upgrade.");
							} else {
								return true;
							}
					} else 
						if ($versionNumberArray[0] < $allowedVersion[0]) {
							showError("This tracker only supports rTorrent ".$allowedVersion[0].".".$allowedVersion[1].".".$allowedVersion[2]." and above. Go to http://libtorrent.rakshasa.no/ to upgrade.");
						} else {
							return true;
						}
				} else
					showError("Tracker code 0x03: You don't appear to be using a valid rTorrent client. See http://libtorrent.rakshasa.no/");
			} else
				showError("Tracker code 0x01: You don't appear to be using a valid rTorrent client. See http://libtorrent.rakshasa.no/");	
		}
	}

	function filterClient($clientVer, $ip, $iplong, $peerid) {
		/*
		 * Okay, check to see if we should allow person on the tracker
		 */
		if (checkAzureus($clientVer)) return true;
		if (checkABC($clientVer)) return true;
		if (checkShadows($clientVer)) return true;
		if (checkBitTornado($clientVer)) return true;
		if (checkNewBitTornado($clientVer)) return true;
		if (checkBitSpirit($clientVer)) return true;
		if (checkTurboBT($clientVer)) return true;
		if (checkLibtorrent($clientVer)) return true;

		/*
		 * This should always be checked LAST.
		 */
		if (checkBitTorrent($clientVer)) return true;

		/*
		 * Hm, let's see if they are even identifying themselves
		 * This bans IPs if they don't!
		 */
		checkforShyClients($clientVer, $ip, $iplong);

		showError("This tracker does not support your client version.");
		return false;
	}
?>