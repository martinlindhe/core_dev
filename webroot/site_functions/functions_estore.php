<?
	function getEstoreBrands(&$db)
	{
		return dbArray($db, 'SELECT * FROM tblEstoreBrands ORDER BY brandName ASC');
	}
	
	function addEstoreBrand(&$db, $name)
	{
		$name = dbAddSlashes($name);
		
		dbQuery($db, 'INSERT INTO tblEstoreBrands SET brandName="'.$name.'"');
	}

	function removeEstoreBrand(&$db, $brand_id)
	{
		if (!is_numeric($brand_id)) return false;
		
		$brand = getEstoreBrand($db, $brand_id);
		
		dbQuery($db, 'DELETE FROM tblEstoreBrands WHERE brandId='.$brand_id);
		dbQuery($db, 'UPDATE tblEstoreObjects SET brandId=0 WHERE brandId='.$brand_id);

		if ($brand['imageId']) {
			$fileinfo = getFileInfo($db, $brand['imageId']);
			$filename = UPLOAD_PATH.$fileinfo['fileName'];
			unlink($filename);
		}
	}

	function getEstoreCategories(&$db, $default_lang = 'EN')
	{
		$list = dbArray($db, 'SELECT * FROM tblEstoreCategories');
		
		for ($i=0; $i<count($list); $i++) {
			$list[$i]['desc'] = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
		}

		return $list;
	}
	
	function addEstoreCategory(&$db, $lang, $name)
	{
		$result = dbQuery($db, 'INSERT INTO tblEstoreCategories SET imageId=0');
		$id = $db['insert_id'];

		addEstoreCategoryLocale($db, $id, $lang, $name);
	}
	
	function addEstoreCategoryLocale(&$db, $id, $lang, $name)
	{
		if (!is_numeric($id)) return false;
		$lang = strtoupper(dbAddSlashes($lang));
		$name = dbAddSlashes($name);

		dbQuery($db, 'INSERT INTO tblEstoreCategoryDescs SET categoryId='.$id.',categoryName="'.$name.'",lang="'.$lang.'"');
	}
	
	function removeEstoreCategoryLocale(&$db, $id, $lang)
	{
		if (!is_numeric($id)) return false;
		$lang = strtoupper(dbAddSlashes($lang));

		dbQuery($db, 'DELETE FROM tblEstoreCategoryDescs WHERE categoryId='.$id.' AND lang="'.$lang.'"');
	}
	
	
	function removeEstoreCategory(&$db, $category_id)
	{
		if (!is_numeric($category_id)) return false;

		dbQuery($db, 'DELETE FROM tblEstoreCategories WHERE categoryId='.$category_id);
	}


	function addEstoreObject(&$db, $category_id, $brand_id, $product_code, $price, $extraprice)
	{
		if (!is_numeric($category_id) || !is_numeric($brand_id) || !is_numeric($extraprice)) return false;

		$product_code = dbAddSlashes($product_code);

		//check $price
		$price = str_replace(',', '.', $price);	//to handle:  1,40 format aswell
		$price = floatval($price);
		if (!$price) return false;

		$result = dbQuery($db, 'INSERT INTO tblEstoreObjects SET categoryId='.$category_id.',brandId='.$brand_id.',productCode="'.$product_code.'",price="'.$price.'",extraPrice="'.$extraprice.'",timeadded='.time());
		return $db['insert_id'];
	}
	
	function updateEstoreObject(&$db, $object_id, $category_id, $brand_id, $product_code, $price, $extraprice)
	{
		if (!is_numeric($object_id) || !is_numeric($category_id) || !is_numeric($brand_id)) return false;
		
		if (!$extraprice) $extraprice = 0;
		
		if (!is_numeric($extraprice)) return false;

		$product_code = dbAddSlashes($product_code);

		//check $price
		$price = str_replace(',', '.', $price);	//to handle:  1,40 format aswell
		$price = floatval($price);
		if (!$price) return false;
		
		dbQuery($db, 'UPDATE tblEstoreObjects SET categoryId='.$category_id.',brandId='.$brand_id.',productCode="'.$product_code.'",price="'.$price.'",extraPrice="'.$extraprice.'" WHERE objectId='.$object_id);
	}
	
	function removeEstoreObjectLocale(&$db, $desc_id)
	{
		if (!is_numeric($desc_id)) return false;
		
		dbQuery($db, 'DELETE FROM tblEstoreObjectDescs WHERE descId='.$desc_id);
		
	}
	
	function removeEstoreObject(&$db, $object_id)
	{
		if (!is_numeric($object_id)) return false;

		dbQuery($db, 'DELETE FROM tblEstoreObjects WHERE objectId='.$object_id);
	}
	
	function addEstoreDesc(&$db, $object_id, $lang, $name, $info, $deliverytime)
	{
		if (!is_numeric($object_id)) return false;
		
		$lang = dbAddSlashes(strtoupper($lang));
		$name = dbAddSlashes($name);
		$info = dbAddSlashes($info);
		$deliverytime = dbAddSlashes($deliverytime);
		
		$sql = 'INSERT INTO tblEstoreObjectDescs SET objectId='.$object_id.',lang="'.$lang.'",name="'.$name.'",info="'.$info.'",deliveryTime="'.$deliverytime.'"';
		$result = dbQuery($db, $sql);
		
		return $db['insert_id'];
	}
	
	function updateEstoreDesc(&$db, $desc_id, $name, $info, $deliverytime)
	{
		if (!is_numeric($desc_id)) return false;
		
		$name = dbAddSlashes($name);
		$info = dbAddSlashes($info);
		$deliverytime = dbAddSlashes($deliverytime);
		
		dbQuery($db, 'UPDATE tblEstoreObjectDescs SET name="'.$name.'",info="'.$info.'",deliveryTime="'.$deliverytime.'" WHERE descId='.$desc_id);
	}
	
	function updateEstoreObjectMainFile(&$db, $object_id, $image_id)
	{
		if (!is_numeric($object_id) || !is_numeric($image_id)) return false;
		
		dbQuery($db, 'UPDATE tblEstoreObjects SET imageId='.$image_id.' WHERE objectId='.$object_id);
	}
	
	function updateEstoreObjectSecondaryFile(&$db, $object_id, $image_id)
	{
		if (!is_numeric($object_id) || !is_numeric($image_id)) return false;

		$result = dbQuery($db, 'INSERT INTO tblEstoreObjectImages SET objectId='.$object_id.',imageId='.$image_id);
		return $db['insert_id'];
	}
	
	function getEstoreObjectSecondaryFiles(&$db, $object_id)
	{
		if (!is_numeric($object_id)) return false;

		return dbArray($db, 'SELECT * FROM tblEstoreObjectImages WHERE objectId='.$object_id);
	}
	
	function removeEstoreObjectSecondaryFile(&$db, $image_id)
	{
		if (!is_numeric($image_id)) return false;
		
		dbQuery($db, 'DELETE FROM tblEstoreObjectImages WHERE imageId='.$image_id);
	}
	
	
	function getEstoreObjects(&$db, $default_lang = 'EN')
	{
		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'ORDER BY productCode ASC';

		$list = dbArray($db, $sql);

		for ($i=0; $i<count($list); $i++) {
			$temp = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
			$list[$i]['categoryName'] = $temp['categoryName'];

			$list[$i]['desc'] = getEstoreObjectLocaleData($db, $list[$i]['objectId'], $default_lang);
		}

		return $list;
	}
	
	function getEstoreObject(&$db, $object_id, $default_lang = 'EN', $all_languages = false)
	{		
		if (!is_numeric($object_id)) return false;

		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.objectId='.$object_id;

		$row = dbOneResult($db, $sql);
		
		$temp = getEstoreCategoryLocaleData($db, $row['categoryId'], $default_lang);
		$row['categoryName'] = $temp['categoryName'];

		if ($all_languages) {
			$list = dbArray($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$object_id);
			for ($i=0; $i<count($list); $i++) {
				$row['desc'][$i] = $list[$i];
			}
		} else {
			$row['desc'] = getEstoreObjectLocaleData($db, $object_id, $default_lang);
		}

		return $row;
	}
	
	function getEstoreObjectImageId(&$db, $object_id)
	{
		if (!is_numeric($object_id)) return false;

		$sql = 'SELECT imageId FROM tblEstoreObjects WHERE objectId='.$object_id;
		return dbOneResultItem($db, $sql);
	}
	
	function getEstoreBrand(&$db, $brand_id)
	{
		if (!is_numeric($brand_id)) return false;
		
		$sql = 'SELECT * FROM tblEstoreBrands WHERE brandId='.$brand_id;
		return dbOneResult($db, $sql);
	}
	
	function updateEstoreBrandName(&$db, $brand_id, $brand_name)
	{
		if (!is_numeric($brand_id)) return false;
		
		$brand_name = dbAddSlashes($brand_name);
		
		dbQuery($db, 'UPDATE tblEstoreBrands SET brandName="'.$brand_name.'" WHERE brandId='.$brand_id);
	}

	function updateEstoreBrandImage(&$db, $brand_id, $image_id)
	{
		if (!is_numeric($brand_id) || !is_numeric($image_id)) return false;

		dbQuery($db, 'UPDATE tblEstoreBrands SET imageId='.$image_id.' WHERE brandId='.$brand_id);
	}
	
	function getEstoreObjectsByBrandCount(&$db, $brand_id)
	{
		if (!is_numeric($brand_id)) return false;
		
		$sql = 'SELECT COUNT(objectId) FROM tblEstoreObjects WHERE brandId='.$brand_id;
		return dbOneResultItem($db, $sql);
	}

	function getEstoreObjectsByCategoryCount(&$db, $category_id)
	{
		if (!is_numeric($category_id)) return false;
		
		$sql = 'SELECT COUNT(objectId) FROM tblEstoreObjects WHERE categoryId='.$category_id;
		return dbOneResultItem($db, $sql);
	}
	
	function getEstoreCategory(&$db, $category_id, $default_lang = 'EN')
	{
		if (!is_numeric($category_id)) return false;
		
		$sql = 'SELECT * FROM tblEstoreCategories WHERE categoryId='.$category_id;
		$data = dbOneResult($db, $sql);

		//default language:
		$data['desc'] = getEstoreCategoryLocaleData($db, $category_id, $default_lang);

		//all languages:
		$data['lang'] = dbArray($db, 'SELECT * FROM tblEstoreCategoryDescs WHERE categoryId='.$category_id);

		return $data;
	}

	function updateEstoreCategoryName(&$db, $category_id, $lang, $category_name)
	{
		if (!is_numeric($category_id)) return false;

		$lang = dbAddSlashes($lang);
		$category_name = dbAddSlashes($category_name);

		dbQuery($db, 'UPDATE tblEstoreCategoryDescs SET categoryName="'.$category_name.'" WHERE categoryId='.$category_id.' AND lang="'.$lang.'"');
	}
	
	function updateEstoreCategoryImage(&$db, $category_id, $image_id)
	{
		if (!is_numeric($category_id) || !is_numeric($image_id)) return false;

		dbQuery($db, 'UPDATE tblEstoreCategories SET imageId='.$image_id.' WHERE categoryId='.$category_id);
	}
	
	//returns $category_id data for one language
	function getEstoreCategoryLocaleData(&$db, $category_id, $default_lang = 'EN')
	{
		if (!is_numeric($category_id)) return false;
		
		$check = dbQuery($db, 'SELECT * FROM tblEstoreCategoryDescs WHERE categoryId='.$category_id.' AND lang="'.$default_lang.'"');
		if (!dbNumRows($check)) {
			$check = dbQuery($db, 'SELECT * FROM tblEstoreCategoryDescs WHERE categoryId='.$category_id.' AND lang="EN"');
			if (!dbNumRows($check)) {
				$check = dbQuery($db, 'SELECT * FROM tblEstoreCategoryDescs WHERE categoryId='.$category_id.' LIMIT 0,1');
			}
		}
		return dbFetchArray($check);
	}
	
	function getEstoreObjectLocaleData(&$db, $object_id, $default_lang = 'EN')
	{
		if (!is_numeric($object_id)) return false;

		$default_lang = dbAddSlashes(strtoupper($default_lang));
		
		$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$object_id.' AND lang="'.$default_lang.'"');
		if (!dbNumRows($check)) {
			$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$object_id.' AND lang="EN"');
			if (!dbNumRows($check)) {
				$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$object_id.' LIMIT 0,1');
			}
		}
		return dbFetchArray($check);
	}

	function getEstoreObjectsByBrand(&$db, $brand_id, $default_lang = 'EN')
	{
		if (!is_numeric($brand_id)) return false;
		
		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.brandId='.$brand_id;
		$list = dbArray($db, $sql);

		for ($i=0; $i<count($list); $i++) {
			
			$temp = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
			$list[$i]['categoryName'] = $temp['categoryName'];
			
			$list[$i]['desc'] = getEstoreObjectLocaleData($db, $list[$i]['objectId'], $default_lang);
		}

		return $list;
	}

	function getEstoreObjectsByCategory(&$db, $category_id, $default_lang = 'EN')
	{
		if (!is_numeric($category_id)) return false;

		$default_lang = dbAddSlashes(strtoupper($default_lang));
		
		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.categoryId='.$category_id;
		$list = dbArray($db, $sql);

		for ($i=0; $i<count($list); $i++) {
			
			$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$list[$i]['objectId'].' AND lang="'.$default_lang.'"');
			if (!dbNumRows($check)) {
				$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$list[$i]['objectId'].' AND lang="EN"');
				if (!dbNumRows($check)) {
					$check = dbQuery($db, 'SELECT * FROM tblEstoreObjectDescs WHERE objectId='.$list[$i]['objectId'].' LIMIT 0,1');
				}
			}
			$list[$i]['desc'] = dbFetchArray($check);
		}

		return $list;
	}

	function getEstoreNewObjects(&$db, $default_lang = 'EN', $timestamp = 0)
	{
		if (!is_numeric($timestamp)) return false;

		if (!$timestamp) {
			$timestamp = time() - 1209600; //14 senaste dagarna dagar
		}

		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'INNER JOIN  tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.timeadded>'.$timestamp;

		$list = dbArray($db, $sql);
		
		for ($i=0; $i<count($list); $i++) {
			
			$temp = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
			$list[$i]['categoryName'] = $temp['categoryName'];
			
			$list[$i]['desc'] = getEstoreObjectLocaleData($db, $list[$i]['objectId'], $default_lang);
		}
		
		return $list;
	}
	
	function getEstoreExtraPriceObjects(&$db, $default_lang = 'EN')
	{
		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'INNER JOIN  tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.extraprice=1';
		$list = dbArray($db, $sql);
		
		for ($i=0; $i<count($list); $i++) {
			
			$temp = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
			$list[$i]['categoryName'] = $temp['categoryName'];
			
			$list[$i]['desc'] = getEstoreObjectLocaleData($db, $list[$i]['objectId'], $default_lang);
		}
		
		return $list;
	}

	/* Given a brand_id, this function returns all categories that has items by this brand, in locale $lang */
	function getEstoreCategoriesByBrand(&$db, $brand_id, $default_lang = 'EN')
	{
		if (!is_numeric($brand_id)) return false;
		$default_lang = dbAddSlashes(strtoupper($default_lang));
		
		$list = dbArray($db, 'SELECT categoryId FROM tblEstoreObjects WHERE brandId='.$brand_id.' GROUP BY categoryId');

		for ($i=0; $i<count($list); $i++) {
			$list[$i]['desc'] = getEstoreCategoryLocaleData($db, $list[$i]['categoryId'], $default_lang);
		}

		return $list;
	}


	function getEstoreObjectsByBrandAndCategory(&$db, $brand_id, $category_id, $default_lang = 'EN')
	{
		if (!is_numeric($brand_id) || !is_numeric($category_id)) return false;

		$sql  = 'SELECT t1.*,t2.brandName FROM tblEstoreObjects AS t1 ';
		$sql .= 'INNER JOIN tblEstoreBrands AS t2 ON (t1.brandId=t2.brandId) ';
		$sql .= 'WHERE t1.brandId='.$brand_id.' AND t1.categoryId='.$category_id;
		$list = dbArray($db, $sql);

		for ($i=0; $i<count($list); $i++) {
			$list[$i]['desc'] = getEstoreObjectLocaleData($db, $list[$i]['objectId'], $default_lang);
		}

		return $list;
	}


	
	/*** OBJECT ATTRIBUTES ***/
	
	function addEstoreObjectAttribute(&$db, $object_id, $lang, $name)
	{
		if (!is_numeric($object_id)) return false;
		
		$lang = dbAddSlashes(strtoupper($lang));
		$name = dbaddSlashes($name);
		
		$attributeId = getRandomId($db, 'tblEstoreObjectAttributes', 'attributeId');
		
		$result = dbQuery($db, 'INSERT INTO tblEstoreObjectAttributes SET objectId='.$object_id.',lang="'.$lang.'",name="'.$name.'",attributeId='.$attributeId);

		return $attributeId;
	}
	
	function addEstoreObjectAttributeOption(&$db, $attribute_id, $option_text)
	{
		if (!is_numeric($attribute_id)) return false;
		$option_text = dbAddSlashes($option_text);
		
		dbQuery($db, 'INSERT INTO tblEstoreObjectAttributeOptions SET attributeId='.$attribute_id.',text="'.$option_text.'"');
	}

	function getEstoreObjectAttributes(&$db, $object_id, $lang = 'EN')
	{
		if (!is_numeric($object_id)) return false;

		$lang = dbAddSlashes(strtoupper($lang));
		$list = dbArray($db, 'SELECT * FROM tblEstoreObjectAttributes WHERE objectId='.$object_id.' AND lang="'.$lang.'"');

		for ($i=0; $i<count($list); $i++) {
			$list[$i]['desc'] = dbArray($db, 'SELECT * FROM tblEstoreObjectAttributeOptions WHERE attributeId='.$list[$i]['attributeId']);
		}
		
		return $list;
	}
	
	function getEstoreObjectAttribute(&$db, $attribute_id)
	{
		if (!is_numeric($attribute_id)) return false;
		
		$list = dbArray($db, 'SELECT * FROM tblEstoreObjectAttributes WHERE attributeId='.$attribute_id);

		for ($i=0; $i<count($list); $i++) {
			$list[$i]['desc'] = dbArray($db, 'SELECT * FROM tblEstoreObjectAttributeOptions WHERE attributeId='.$list[$i]['attributeId']);
		}
		
		return $list;
	}

	function updateEstoreObjectAttributeLocale(&$db, $attribute_id, $lang, $name)
	{
		if (!is_numeric($attribute_id)) return false;
		
		$lang = dbAddSlashes(strtoupper($lang));
		$name = dbAddSlashes($name);
		
		dbQuery($db, 'UPDATE tblEstoreObjectAttributes SET name="'.$name.'" WHERE attributeId='.$attribute_id.' AND lang="'.$lang.'"');
	}
	
	function updateEstoreObjectAttributeOptionLocale(&$db, $option_id, $text)
	{
		if (!is_numeric($option_id)) return false;
		
		$text = dbAddSlashes($text);
		
		dbQuery($db, 'UPDATE tblEstoreObjectAttributeOptions SET text="'.$text.'" WHERE optionId='.$option_id);
	}
	
	function getEstoreObjectAttributeOption(&$db, $option_id)
	{
		if (!is_numeric($option_id)) return false;
		
		$sql = 'SELECT * FROM tblEstoreObjectAttributeOptions WHERE optionId='.$option_id;
		return dbOneResult($db, $sql);
	}

?>