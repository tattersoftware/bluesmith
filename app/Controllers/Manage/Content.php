<?php namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Models\PageModel;
use App\Models\MaterialModel;
use App\Models\MethodModel;

class Content extends BaseController
{
	// Dynamic page content
	public function page($name = 'Home')
	{
		$pages = new PageModel();
		
		// Check for form submission
		if ($post = $this->request->getPost()):
			$page = $pages->where('name', $post['name'])->first();
			
			$page->content = $post['content'];
			$pages->save($page);
			
			alert('success', "'{$page->name}' page updated.");
			
		// Load current values		
		else:
			$page = $pages->where('name', $name)->first();
		endif;		
		
		$data = [
			'name'    => $page->name,
			'content' => $page->content,
		];
		return view('content/page', $data);
	}
	
	// Controls for individual settings related to site branding
	public function branding()
	{
		// Preload the Settings Library
		$data['settings'] = service('settings');
		helper('date');
		
		// Check for form submission
		if ($post = $this->request->getPost()):
			$page = $pages->where('name', $post['name'])->first();
			
			$page->content = $post['content'];
			$pages->save($page);
			
			if ($this->request->isAJAX()):
				echo 'success';
				return;
			endif;
			
			alert('success', "'{$page->name}' page updated.");

		endif;		
		
		return view('content/branding', $data);
	}
}
