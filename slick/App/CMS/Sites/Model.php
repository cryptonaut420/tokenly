<?php
class Slick_App_CMS_Sites_Model extends Slick_Core_Model
{

	public function getSiteForm($siteId = 0)
	{
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Site Name');
		$form->add($name);
		
		$domain = new Slick_UI_Textbox('domain');
		$domain->addAttribute('required');
		$domain->setLabel('Domain');
		$form->add($domain);	

		$url = new Slick_UI_Textbox('url');
		$url->addAttribute('required');
		$url->setLabel('URL');
		$form->add($url);	

		$isDefault = new Slick_UI_Checkbox('isDefault');
		$isDefault->setBool(1);
		$isDefault->setValue(1);
		$isDefault->setLabel('Default site?');
		$form->add($isDefault);

		$image = new Slick_UI_File('image');
		$image->setLabel('Site Image');
		$form->add($image);

		if($siteId != 0){
			$apps = new Slick_UI_CheckboxList('apps');
			$apps->setLabel('Site Apps');
			$apps->setLabelDir('R');
			$getGroups = $this->getAll('apps');
			foreach($getGroups as $app){
				$apps->addOption($app['appId'], $app['name']);
			}
			
			$form->add($apps);
		}

		return $form;
	}


	
	public function addSite($data)
	{
		$req = array('name', 'isDefault', 'domain', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('sites', $useData);
		if(!$add){
			throw new Exception('Error adding site');
		}
		
		$this->updateSiteImage($add);
		
		return $add;
		
		
	}
		
	public function editSite($id, $data)
	{
		$req = array('name', 'isDefault', 'domain', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$edit = $this->edit('sites', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing site');
		}
		
		$this->delete('site_apps', $id, 'siteId');
		foreach($data['apps'] as $app){
			$this->insert('site_apps', array('siteId' => $id, 'appId' => $app));
		}
		
		$this->updateSiteImage($id);
		
		return true;
		
	}
	
	public function getSiteApps($siteId)
	{
		$get = $this->getAll('site_apps', array('siteId' => $siteId));
		$output = array();
		foreach($get as $row){
			$output[] = $row['appId'];
		}
		
		return $output;
	}


	public function updateSiteImage($id)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != false){

			$name = $id.'-'.hash('sha256', $_FILES['image']['name'].$id).'.jpg';
			$path = SITE_PATH.'/files/sites/'.$name;
			$resize = Slick_Util_Image::resizeImage($_FILES['image']['tmp_name'], $path, 0, 0);
			if($resize){
				$update = $this->edit('sites', $id, array('image' => $name));
				if($update){
					return true;
				}
			}
			
		}
		return false;
		
	}


}

?>