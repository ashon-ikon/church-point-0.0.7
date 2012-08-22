<?php

class ArticlesController extends Point_Controller_Action
{
	protected	$_XmlResponse 		= null;
	protected	$_user_priviledge 	= 'guest';
	protected	$_articleErrors		= array();
	protected	$_user				= null;
	
	protected	$_articles_group_id	= null;
	protected	$_news_group_id		= null;
	
	public function preDispatch()
    {
    	
    	
    	$request = $this->getRequest();
    	
    	$user 						= Point_Model_User::getInstance();
    	$groups 					= Point_Model_Groups::getInstance();
		$this->_user_priviledge		= $groups->getMembership($user->getUserId());
    	$this->_user 				= $user;
   		 
   		
   		$action_name = $request->getActionName();
   		
   		    
    	if (!$user->isLoggedIn() && 
    		('index' 		!= $action_name && 
			 'news'	 		!= $action_name && 
			 'allarticles' 	!= $action_name && 
			 'allnews' 		!= $action_name && 
			 'search' 		!= $action_name && 
			 'articles' 	!= $action_name))
    	
    		$this->_redirect('error/noaccess');
    	
    	
    	/* Check if the request is edit */
    	if ('edit' == $request->getActionName())
    	{
    		
    		/* Ensure the user is logged in first */
    		if ($user->isLoggedIn())
    		{
    			/* Check if user has the ability to edit page */
    			$article_id = $request->getParam('a_id', null);
    			if(is_numeric($article_id))
    			{
    				$article 					= Point_Model_Article::getInstance();
//    				$this->_user_priviledge 	= $article->getPriviledge($article_id, $user->getUserId());
    			
    			}
    		}else
	   			/* User is not previledged*/
	   			$this->_redirect('error/noaccess');
    	}
    	
    }
	
    public function init()
    {
    	$request = $this->getRequest();
    	
    	/**
    	 * ---------------------------------------------------------------
    	 * If the request is of Ajax type then disable layout in response.
    	 * ---------------------------------------------------------------
    	 */
    	if ($request->isXmlHttpRequest())
    	{
	    	$this->getResponse()
				 ->setHeader('Cache-Control',
							 'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0')
				 ->setHeader('Expires', 'Tue, 14 Aug 1997 10:00:35 GMT');
	    	
	    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
	    	$ajaxContext->addActionContext('request', 'html')
						->initContext();
						
	    	// Both layout and view renderer should be disabled
	    	Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
	    	Zend_Layout::getMvcInstance()->disableLayout();

    	}
    	
    	/* Get content groups info */
    	$contentGroups				= Point_Model_ContentGroups::getInstance();
    	
    	$this->_articles_group_id 	= $contentGroups->getGroupIdByKeyword('articles');
    	$this->_news_group_id 		= $contentGroups->getGroupIdByKeyword('news');
    	
    }
    
    
    protected function _getXmlResponse()
    {
    	if ( null === $this->_XmlResponse )
    	{
    		$this->_XmlResponse = new Point_Object_AjaxResponse();
    	}
    	return $this->_XmlResponse;
    }
    
    public function indexAction()
    {
		$this->_setTitle('Articles and News');
		
		$news_obj				= Point_Model_News::getInstance();		
		$articles_obj			= Point_Model_Article::getInstance();
		$base_url				= $this->view->fullBaseUrl() ;
		
		/* Get the privildege for this article */
    	$user					= Point_Model_User::getInstance();
    	$contentGroups			= Point_Model_ContentGroups::getInstance();
    		
    	/* Check if user is either an editor or admin */
    	$user_access			= $contentGroups->getMembership($user->getUserId(), $articles_obj->getContentGroupId());
    	
    	$user_can_edit			= false;
    	
		if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
			Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
		{
			$user_can_edit		= true;
		}
		
		$all_news					= $news_obj->getTopNews();
		if (is_array($all_news))
		{
			/* inject url */
			foreach ($all_news as &$news)
			{
				$news['news_url'] 		= $base_url . '/articles/news/' . $news_obj->getNewsUrl($news['news_id']);
				if ($user_can_edit)
				{
					$news['news_edit_link'] = $this->view->fullUrl(array('controller'=> 'articles', 'action'=>'editnews', 'n_id'=>$news['news_id']), false, null);
					$news['news_remove_link'] = $this->view->fullUrl(array('controller'=> 'articles', 'action'=>'removenews', 'n_id'=>$news['news_id']), false, null);
				}
			}
		}
		
		$all_articles				= $articles_obj->getTopArticles();
		if (is_array($all_articles))
		{
			/* inject url */
			foreach ($all_articles as &$articles)
			{
				$articles['article_url'] 	= $base_url . '/articles/articles/' . $articles_obj->getArticleUrl($articles['article_id']);
				if ($user_can_edit)
				{
					$articles['article_edit_link'] = $this->view->fullUrl(array('controller'=> 'articles', 'action'=>'editarticle', 'a_id'=>$articles['article_id']), false, null);
					$articles['article_remove_link'] = $this->view->fullUrl(array('controller'=> 'articles', 'action'=>'removearticle', 'a_id'=>$articles['article_id']), false, null);
				}
			}
		}

		$this->view->news		= $all_news;
		$this->view->articles	= $all_articles;
		
    }

	public function articlesAction()
	{
		
		$file  	= null;	
		
		$articles 	= Point_Model_Article::getInstance();
//		$this->getResponse()->setUri('testing/test');

		/**
    	 *  Get incoming parameters 
    	 *---------------------------*/
    	$request = $this->getRequest();
		
		$file 			= $request->getParam('f', null);
	
		/* Redirect user to latest known article */
		if (!$file)
			$this->_redirect('articles/articles/'. $articles->getLatestUrl());
		$fullUrl = $request->getScheme().'://'.$request->getHttpHost() . $this->view->url().'/?f='.$file;
		
    	$article =	$articles->getArticle(array('file'=> $file), true);
    	
    	if (!$article)
    	{
			
    		$article = '';
    		
    		$this->_redirect('articles/articles/'. $articles->getLatestUrl());
    	}
    	else
    	{
    		
    		/* setup view stuff */
    		$this->view->author 	= $article['user_fname'] . ' '. $article['user_lname']; 
    		$this->view->fullUrl 	= $fullUrl; 
    		 
    		
    		/* Get the privildege for this article */
    		$user			= Point_Model_User::getInstance();
    		$contentGroups	= Point_Model_ContentGroups::getInstance();
    		
    		/* Check if user is either an editor or admin */
    		$user_access	= $contentGroups->getMembership($user->getUserId(), $articles->getContentGroupId());
    		
    		if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
	    		Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
    		{
    			$this->view->edit_link = $this->view->url(array('controller'=> 'articles', 'action'=>'editarticle', 'a_id'=>$article['article_id']), false, null);
    		}
    		
    		$this->_setPageDescription(getShowcaseText(remove_html_tags($article['article_content'])), 400);
	    	
	    	if($request->isXmlHttpRequest())
	    	{
		    	/* we have recieved Ajax response */
	    	}
	    	else
	    	{
		    	$this->view->article = $article;
		    	if (!empty($article) && array_key_exists('article_title', $article))
		    	{
			    	$this->_setTitle('Articles and News');
			    	$this->_enableSocialSharing();
		    	}
	    	}
    	}
	}
	
    public function newsAction()
    {
        $file  	= null;	
		
		$news_obj 	= Point_Model_News::getInstance();

		/**
    	 *  Get incoming parameters 
    	 *---------------------------*/
    	$request = $this->getRequest();
		
		$file 			= $request->getParam('n', null);
	
		/*
		 * TODO: Redirect list of news
		 * DONE: articles/allnews
		 */

		/* Redirect user to latest known news  */
		if (!$file)
			$this->_redirect('articles/news'. $news_obj->getLatestUrl());
			
		$fullUrl = $request->getScheme().'://'.$request->getHttpHost() . $this->view->url().'/?n='.$file;
		
    	$news =	$news_obj->getNews(array('file'=> $file), true);
    	
    	if (!$news)
    	{
    		$news = '';
    		
    		$this->_redirect('articles/news'. $news_obj->getLatestUrl());
    	}
    	else
    	{
    		
    		/* setup view stuff */
    		$this->view->author 	= $news['user_fname'] . ' '. $news['user_lname']; 
    		$this->view->fullUrl 	= $fullUrl; 
    		 
    		
    		/* Get the privildege for this news */
    		$user_access = $this->getNewsPriviledge($news);
    		 
    		
    		if (Point_Model_ContentGroups::GROUP_EDITOR == $user_access ||  
    			Point_Model_ContentGroups::GROUP_ADMIN == $user_access  )
    		{
    			$this->view->edit_link = $this->view->url(array('controller'=> 'articles', 'action'=>'editnews', 'n_id'=>$news['news_id']), false, null);
    		}
	    	
	    	if($request->isXmlHttpRequest())
	    	{
		    	/* we have recieved Ajax response */
	    	}
	    	else
	    	{
		    	$this->view->news = $news;
		    	if (!empty($news) && array_key_exists('news_title', $news))
			    	$this->_setTitle('Community Sharing');
	    	}
    	}
    }

    public function previewAction()
    {
        // action body
    }

    public function allarticlesAction()
    {
    	$request 		= $this->getRequest();
		
		$articles_obj	= Point_Model_Article::getInstance();
			
		/* Retrieve all the articles */
		$start 			= $request->getParam('s', 0);
		
		$amount			= $request->getParam('m', 10);
		
		$found_articles	= $articles_obj->getArticles($start, $amount);
		
		$baseUrl		= $this->_getBaseUrl();
		
		$this->_setTitle('Articles');
		
		if (!empty($found_articles))
		{
			/* For each found articles make the view link and remove link */
			foreach($found_articles as &$article)
			{
				$url						= ''.	date('Y/m/d', strtotime($article['article_date']));
				$url						.= '/'. $article['article_seo_title'];
				
				$article_url 				= $baseUrl.'/articles/articles/?f='. urlencode($url);
				$article_remove_url 		= $baseUrl.'/articles/removearticles?a_id='.$article['article_id'];
				$article['article_url'] 	= $article_url;	
				$article['article_remove_url'] 	= $article_remove_url;	
			}
			
			$this->view->articles 		= $found_articles;
		
		
			/* Allow editing if user is a within article editors group */
			$contentGroups	= Point_Model_ContentGroups::getInstance();
			
			$user_id	 	= Point_Model_User::getInstance()->getUserId();
			
			$articleGroupId 	= $this->_articles_group_id;
			
			$user_access 	= $contentGroups->getMembership($user_id, $articleGroupId);
			
			$this->view->edit_access	= $user_access != Point_Model_ContentGroups::GROUP_GUEST ? true : false;
		}	
    }

    public function addarticlesAction()
    {
    
        $request 		= $this->getRequest();
    	$article_obj	= Point_Model_Article::getInstance();
    	$user			= Point_Model_User::getInstance();
    	$contentGroups	= Point_Model_ContentGroups::getInstance();
    	
    	/* Check if user is either an editor or admin */
    	$user_access	= $contentGroups->getMembership($user->getUserId(), $article_obj->getContentGroupId());
    	
    	if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
    		Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
    	{
	    	$this->_setTitle('Add Article');
	    	
	    	$editor_raw_content	= $request->getParam('tinymcetarea',null);
	    	$form 	 			= new Point_Form_ArticleForm();
	    	$tinymce 			= new Point_Form_TinyMCE($form);
	    	$raw_data			= $request->getPost();
	    	
	    	/* check if form is valid */
	    	if ($request->isPost())
	    	{
	    		
		    	if($form->isValid($raw_data))
		    	{
			    	$clean_data = $form->getValues();		
			    	
			    	$content 		= $clean_data['tinymcetarea'];
			    	$title 	 		= $clean_data['title'];
			    	$article_desc 	= $clean_data['title_desc'];
			    	$article_tags 	= $clean_data['tags'];
			    	
			    	/* Clean up tags */
	    				$article_tags 		= cleanReplace($article_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
			    	$article_tags 		= explode(',', $article_tags); /* make array form */
			    	
			    	/* Check if we have a post */
			    	if ($request->getParam('submit', null))
			    	{
				    	/* check if form is valid */
				    	/**
				    	 * public function addNew($content , $title,$tags = array() , $author_id = null, $published = true ,$group_id = 1 )
				    	 */
				    	$article_obj	= Point_Model_Article::getInstance();
						$result			=  	$article_obj->addNew($content, 
									    						 $title,
									    						 $article_desc,
									    						 $article_tags, 
									    						 $this->_user->getUserId(),
																 /* TODO: Publish? Get from form   */ true 
									    						 /* TODO: Group_id ? Get from form */ );

				    	/* If valid attempt to add new article */
						if (false !==$result)
						{
							$msg = 'Article added successfully';
							$this->view->successmsg = $msg;
							$this->_redirect('articles/articles' . $article_obj->getArticleUrl((int)$result));
						}else
						{
							$this->_articleErrors[] = 'Unable to edit artilce.';
						}
			    	}
			    	else if ( $request->getParam('preview', null))
			    	{
				    	/* Do the preview */
				    	$user 	= $this->_user;
				    	
				    	/* setup view stuff */
				    	$form->populate($request->getPost());
				    	$this->view->preview_author 	= $user->getFullname();
				    	
				    	$this->view->preview_title 		= $request->getParam('title', null);
				    	$this->view->preview_article 	= $request->getParam('tinymcetarea', null);
				    	$this->view->preview_time 		= time();
			    	}
		    	}
		    	else{
			    	/* Invalid form */
			    	$this->_articleErrors[] = 'Invalid or Incomplete form received.';
		    	}
	    	}
	    	
	    	/* Prepare editing form */
	    	
	    	$form->getElement('title')->setValue($request->getParam('title', null));
	    	$article_textarea	= $form->getElement('tinymcetarea');
	    	$article_textarea->setValue($editor_raw_content);
	    	$form->getElement('tags')->setValue($request->getParam('tags', null));
	    	$this->view->form 	=	$form;
	    	$this->view->article_editor =	$tinymce->makeForm($editor_raw_content, 'article-'. genRandomNumString(2) );
	    	    	
    	if (!empty($this->_articleErrors))
    		$this->view->errormsgs = $this->_articleErrors;    
    	}
    }

    public function removearticleAction()
    {
        $request 	= $this->getRequest();
		
		/* Get incoming param */
		$article_id 	= $request->getParam('a_id' , null);
		
		if ($article_id)
		{
			$this->_setTitle('Remove Article');
			
			$articles_obj	= Point_Model_Article::getInstance();
			
			$found_article	= $articles_obj->getArticleById($article_id);
			
					
			if ($found_article)
			{
				/* Create a remove form */
				$remove_form		= new Point_Form_ArticleRemove();
		
				/* Make url just for extra confirmation */
				$baseUrl		= $this->_getBaseUrl();
				
				$article_url		= $baseUrl.'/articles/articles?f='. $this->view->escape( 
										date('Y/m/d', strtotime( $found_article['article_date'] ) ) . '/'.$found_article['article_seo_title']
									);
									
				$found_article['article_url'] = $article_url;
				
				$this->view->article = $found_article;
				
				$this->view->form	= $remove_form;
				
				
				if ($request->isPost() && $request->getParam('remove', false))
				{
					/* We have a valid removal request... so let's do it :) */
					if ($articles_obj->remove($article_id))
					{
						$successmsg 	= 'Article removed <strong>successfully</strong>';
						
						$this->view->successmsg = $successmsg;
						
						/* Remove form from view */
						$this->view->form		= false; 
					}
				}
				else if ($request->isPost() && $request->getParam('cancel', false))
				{
					/* Editor changed his/mind */
					
					/* Redirect to new article*/
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl('/articles/articles/')->redirectAndExist();
					
				}
				else
				{
					/* Add article id to param and re-show */
					$remove_form->getElement('s_id')->setValue($article_id);	
				}
				
				
				
			}
		}
		else
		{
			$this->_wordErrors[] = 'No valid article selected for removal.<br /><br />Please select a article to remove';	
		} 
		
		if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;
    }
    
    public function removenewsAction()
    {
        
        $request 	= $this->getRequest();
		
		/* Get incoming param */
		/* Get the id */
		$news_id 	= $request->getParam('n_id' , null);
		
		if ($news_id)
		{
			$this->_setTitle('Remove Sermon');
			
			$news_obj	= Point_Model_News::getInstance();
			
			$found_news	= $news_obj->getNewsById($news_id);
			
			if ($found_news)
			{
				/* Create a remove form */
				$remove_form		= new Point_Form_NewsRemove();
		
				/* Make url just for extra confirmation */
				$baseUrl		= $this->_getBaseUrl();
				
				$news_url		= $baseUrl.'/articles/news?f='. $this->view->escape( 
										/*$found_news['news_id'] . '/' .*/
										date('Y/m/d', strtotime( $found_news['news_date'] ) ) . '/'.$found_news['news_seo_title']
									);
									
				$found_news['news_url'] = $news_url;
				
				$this->view->news = $found_news;
				
				$this->view->form	= $remove_form;
				
//				echo '<pre>', print_r($request->getParams(), true),'</>';
				/* Add news id to param and re-show */
				$remove_form->getElement('n_id')->setValue($news_id);
					
				if ($request->isPost() && $request->getParam('remove', false))
				{
					/* We have a valid removal request... so let's do it :) */
					if ($news_obj->remove($news_id))
					{
						$successmsg 	= 'News removed <strong>successfully</strong>';
						
						$this->view->successmsg = $successmsg;
						
						/* Remove form from view */
						$this->view->form		= false; 
					}
				}
				else if ($request->isPost() && $request->getParam('cancel', false))
				{
					/* Editor changed his/mind */
					
					/* Redirect to new article*/
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl('/articles/allnews')->redirectAndExist();
					
				}
				else
				{
					/* Add news id to param and re-show */
					$remove_form->getElement('n_id')->setValue($news_id);	
				}
				
				
				
			}
		}
		else
		{
			$this->_wordErrors[] = 'No valid news item selected for removal.<br /><br />Please select a news item to remove';	
		} 
		
		if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;
    }
    
    public function editarticleAction()
    {
    	$request 		= $this->getRequest();
    	$article_obj	= Point_Model_Article::getInstance();
    	$user_access	= $this->_user_priviledge;
    	
    	
    	if ($article_id = $request->getParam('a_id', null))
    	{
	    	$contentGroups	= Point_Model_ContentGroups::getInstance();
	    	$user			= Point_Model_User::getInstance();
	    	
	    	/* Check if user is either an editor or admin */
	    	$user_access	= $contentGroups->getMembership($user->getUserId(), $article_obj->getContentGroupId());
	    	
	    	if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
		    	Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
    		{
    			$this->_setTitle('Edit Article');
    			/* retrieve form */
    			$article 			= 	$article_obj->getArticleById($article_id);	
    			/* Prepare editing form */
    			$form 	 			= new Point_Form_ArticleForm();
    			$tinymce 			= new Point_Form_TinyMCE($form);

    			$form->getElement('title')->setValue($article['article_title']);
    			$article_textarea	= $form->getElement('tinymcetarea');
    			$article_textarea->setValue($article['article_content']);
     			$form->getElement('tags')->setValue($article['article_tags']);
     			$form->getElement('title_desc')->setValue($article['article_desc']);
     			$this->view->form 	=	$form;
    			$this->view->article=	$article;
    			$this->view->article_editor =	$tinymce->makeForm($article['article_content'], 'article-'.$article_id );
    			
    			/* Check if it's Preview */
    			if ($request->isPost() && $request->getParam('preview', null))
    			{
    				/* setup view stuff */
    				$form->populate($request->getPost());
	    			$this->view->preview_author 	= $article['user_fname'] . ' '. $article['user_lname'];
	    			 
	    			$this->view->preview_title 		= $request->getParam('title', null);
	    			$this->view->preview_article 	= $request->getParam('tinymcetarea', null);
	    			$this->view->preview_time 		= time();
    			}
    			else if ($request->isPost() && $request->getParam('submit', null))
    			{
    				/* Submit coming through */
    				$raw_data = $request->getPost();
    				if ($form->isValid($raw_data))
    				{
	    				$clean_data 	= $form->getValues();
	    				
	    				/* Now let's save each of the images one after the other */
	    				$article_obj	= Point_Model_Article::getInstance();
	    				//addNew($content , $title,$tags = array() , $published = true ,$group_id = 1 /* 1 => public */)
	    				$article_content 	= $clean_data['tinymcetarea'];
	    				$article_title 		= $clean_data['title'];
	    				$article_desc 		= $clean_data['title_desc'];
	    				$article_tags 		= $clean_data['tags'];
	    				
	    				/* Clean up tags */
	    				$article_tags 		= cleanReplace($article_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
	    				$article_tags 		= explode(',', $article_tags); /* make array form */
	    				
	    				$result = $article_obj->updateArticle($article_content, $article_title,
	    									$article_desc, 
	    									$article_tags, 
	    									$article_id,
	    									/* TODO: Publish? Get from form   */ true 
	    									/* TODO: Group_id ? Get from form */ );
	    				if (false !==$result)
	    				{
	    					$msg = 'Article updated successfully';
		    				$this->view->successmsg = $msg;
		    				$this->_redirect('articles/articles' . $article_obj->getArticleUrl($article_id));
	    				}else
	    				{
	    					$this->_articleErrors[] = 'Unable to edit artilce.';
	    				}
    				}
    				else
    				{
	    				$form->populate($raw_data);
	    				$form->getElement('title')->setValue($request->getParam('title', null));
	    				$article_textarea	= $form->getElement('tinymcetarea');
	    				$article_textarea->setValue($request->getParam('tinymcetarea', null));
	    				$this->view->form 	=	$form;
	    				$this->view->article=	$article;
	    				$this->view->article_editor =	$tinymce->makeForm($article['article_content'], 'article-'.$article_id );
	    				$errors = $form->getMessages();
	    				$preparedErrors = array();
	    				if (is_array($errors))
	    				{
		    				foreach($errors as $element => $msg)
							{
			    				if (is_array($msg))
			    				{
				    				foreach($msg as $msg_this)
									{
					    				$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
									}
			    				}
			    				else
				    				$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
							}
	    				}
	    				//$preparedErrors [] = $msgs;
	    				$this->_articleErrors =  $preparedErrors;
	    				$this->_articleErrors[] = 'Invalid or incomplete form submitted';
    				}	
    			}
    		}
    		else
    		{
    			$this->_forward('articles', null,array('f' => $article_obj->getLatestUrl()));
    		}
    	}else
    		$this->view->error_message = 'Unknown article';
    	if (!empty($this->_articleErrors))
    		$this->view->errormsgs = $this->_articleErrors;
    }
    
    
    public function allnewsAction()
    {
    	$request 		= $this->getRequest();
		
		$news_obj	= Point_Model_News::getInstance();
			
		/* Retrieve all the news */
		$start 			= $request->getParam('s', 0);
		
		$amount			= $request->getParam('m', 10);
		
		$found_news		= $news_obj->getAllNews($start, $amount);
		
		$baseUrl		= $this->_getBaseUrl();
		
		$this->_setTitle('News');
		
		if (!empty($found_news))
		{
			/* For each found news make the view link and remove link */
			foreach($found_news as &$news)
			{
				$url						= ''.	date('Y/m/d', strtotime($news['news_date']));
				$url						.= '/'. $news['news_seo_title'];
				
				$news_url 				= $baseUrl.'/articles/news/?n='. urlencode($url);
				$news_remove_url 		= $baseUrl.'/articles/removenews?n_id='.$news['news_id'];
				$news['news_url'] 	= $news_url;	
				$news['news_remove_url'] 	= $news_remove_url;	
			}
			
			$this->view->news 		= $found_news;
		
		
			/* Allow editing if user is a within news editors group */
			$contentGroups	= Point_Model_ContentGroups::getInstance();
			
			$user_id	 	= Point_Model_User::getInstance()->getUserId();
			
			$newsGroupId 	= $this->_news_group_id;
			
			$user_access 	= $contentGroups->getMembership($user_id, $newsGroupId);
			
			$this->view->edit_access	= $user_access != Point_Model_ContentGroups::GROUP_GUEST ? true : false;
		}	
    }

	public function editnewsAction()
    {
    	$request 		= $this->getRequest();
    	$news_obj		= Point_Model_News::getInstance();
    	
    	
    	if ($news_id = $request->getParam('n_id', null))
    	{
    		$contentGroups	= Point_Model_ContentGroups::getInstance();
	    	$user			= Point_Model_User::getInstance();
	    	
	    	/* Check if user is either an editor or admin */
	    	$user_access	= $contentGroups->getMembership($user->getUserId(), $news_obj->getContentGroupId());
	    	
	    	if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
		    	Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
    		{
    			$this->_setTitle('Edit News for the Community');
    			/* retrieve form */
    			$news 			= 	$news_obj->getNewsById($news_id);	
    			/* Prepare editing form */
    			$form 	 			= new Point_Form_NewsForm();
    			$tinymce 			= new Point_Form_TinyMCE($form);

    			$form->getElement('title')->setValue($news['news_title']);
    			$news_textarea	= $form->getElement('tinymcetarea');
    			$news_textarea->setValue($news['news_content']);
     			$form->getElement('content_desc')->setValue($news['news_desc']);
     			$form->getElement('tags')->setValue($news['news_tags']);
     			
     			
     			$this->view->form 	=	$form;
    			$this->view->news=	$news;
    			$this->view->news_editor =	$tinymce->makeForm($news['news_content'], 'news-'.$news_id );
    			
    			/* Check if it's Preview */
    			if ($request->isPost() && $request->getParam('preview', null))
    			{
    				/* setup view stuff */
    				$form->populate($request->getPost());
	    			$this->view->preview_author 	= $news['user_fname'] . ' '. $news['user_lname'];
	    			 
	    			$this->view->preview_title 		= $request->getParam('title', null);
	    			$this->view->preview_news 	= $request->getParam('tinymcetarea', null);
	    			$this->view->preview_time 		= time();
    			}
    			else if ($request->isPost() && $request->getParam('submit', null))
    			{
    				/* Submit coming through */
    				$raw_data = $request->getPost();	
    				if ($form->isValid($raw_data))
    				{
	    				$clean_data 	= $form->getValues();
	    				
	    				/* Now let's save each of the images one after the other */
	    				$news_obj	= Point_Model_News::getInstance();
	    				//addNew($content , $title,$tags = array() , $published = true ,$group_id = 1 /* 1 => public */)
	    				$news_content 		= $clean_data['tinymcetarea'];
	    				$news_content_desc 	= $clean_data['content_desc'];
	    				$news_title			= $clean_data['title'];
	    				
	    				$news_tags 			= $clean_data['tags'];
	    				
	    				/* Clean up tags */
	    				$news_tags 			= cleanReplace($news_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
	    				$news_tags 			= explode(',', $news_tags); /* make array form */
	    				
	    				
	    				$result = $news_obj->updateNews($news_id, $news_title, $news_content, $news_content_desc, 
	    									
	    									$news_tags,
	    									/* TODO: Publish? Get from form   */ true 
	    									/* TODO: Group_id ? Get from form */ );
	    				if (false !==$result)
	    				{
	    					$msg = 'News updated successfully';
		    				$this->view->successmsg = $msg;
		    				$this->_redirect('articles/news' . $news_obj->getNewsUrl($news_id));
	    				}else
	    				{
	    					$this->_newsErrors[] = 'Unable to edit artilce.';
	    				}
    				}
    				else
    				{
	    				$form->populate($raw_data);
	    				$form->getElement('title')->setValue($request->getParam('title', null));
	    				$news_textarea	= $form->getElement('tinymcetarea');
	    				$news_textarea->setValue($request->getParam('tinymcetarea', null));
	    				$this->view->form 	=	$form;
	    				$this->view->news=	$news;
	    				$this->view->news_editor =	$tinymce->makeForm($news['news_content'], 'news-'.$news_id );
	    				$errors = $form->getMessages();
	    				$preparedErrors = array();
	    				if (is_array($errors))
	    				{
		    				foreach($errors as $element => $msg)
							{
			    				if (is_array($msg))
			    				{
				    				foreach($msg as $msg_this)
									{
					    				$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
									}
			    				}
			    				else
				    				$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
							}
	    				}
	    				//$preparedErrors [] = $msgs;
	    				$this->_newsErrors =  $preparedErrors;
	    				$this->_newsErrors[] = 'Invalid or incomplete form submitted';
    				}	
    			}
    		}
    		else
    		{
    			$this->_forward('news', null,array('f' => $news_obj->getLatestUrl()));
    		}
    	}else
    		$this->view->error_message = 'Unknown news';
    	if (!empty($this->_newsErrors))
    		$this->view->errormsgs = $this->_newsErrors;
    }

    
    public function addnewsAction()
    {
    
        $request 		= $this->getRequest();
    	$news_obj		= Point_Model_News::getInstance();
    	$user			= Point_Model_User::getInstance();
    	
    	$this->_setTitle('Add News for the Community');
    	
    	$editor_raw_content	= $request->getParam('tinymcetarea',null);
    	$form 	 			= new Point_Form_NewsForm();
    	$tinymce 			= new Point_Form_TinyMCE($form);
    	$raw_data			= $request->getPost();
    	$contentGroups	= Point_Model_ContentGroups::getInstance();
	    	$user			= Point_Model_User::getInstance();
	    	
	    	/* Check if user is either an editor or admin */
	    	$user_access	= $contentGroups->getMembership($user->getUserId(), $news_obj->getContentGroupId());
	    	
	    	if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
		    	Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
		    	{
		    	/* check if form is valid */
		    	if ($request->isPost())
		    	{
			    	
			    	if($form->isValid($raw_data))
			    	{
				    	$clean_data = $form->getValues();		
				    	
				    	$content 		= $clean_data['tinymcetarea'];
				    	$content_desc	= $clean_data['content_desc'];
				    	$title 	 		= $clean_data['title'];
				    	$news_tags 		= $clean_data['tags'];

				    	/* Clean up tags */
				    	$news_tags 		= cleanReplace($news_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
				    	$news_tags 		= explode(',', $news_tags); /* make array form */
				    	
				    	/* Check if we have a post */
				    	if ($request->getParam('submit', null))
				    	{
					    	/* check if form is valid */
					    	/**
					    	 * addNew($content , $title, $tags = array() , $author_id = null, $published = true )
					    	 */
					    	$news_obj	= Point_Model_News::getInstance();
					    	$result			=  	$news_obj->addNew( 
											    	$title,
											    	$content,
											    	$content_desc,
													$news_tags, 
													$user->getUserId(),
													/* TODO: Publish? Get from form   */ true 
										    		/* TODO: Group_id ? Get from form */ );
										    	
							/* If valid attempt to add new news */
					    	if (false !==$result)
					    	{
						    	$msg = 'News added successfully';
						    	$this->view->successmsg = $msg;
						    	$this->_redirect('articles/news' . $news_obj->getNewsUrl((int)$result));
					    	}else
					    	{
						    	$this->_newsErrors[] = 'Unable to add news.';
					    	}
				    	}
				    	else if ( $request->getParam('preview', null))
				    	{
					    	/* Do the preview */
					    	
					    	/* setup view stuff */
					    	$form->populate($request->getPost());
					    	$this->view->preview_author 	= $user->getFullname();
					    	
					    	$this->view->preview_title 		= $request->getParam('title', null);
					    	$this->view->preview_news 		= $request->getParam('tinymcetarea', null);
					    	$this->view->preview_time 		= time();
				    	}
			    	}
			    	else{
				    	/* Invalid form */
				    	$this->_newsErrors[] = 'Invalid or Incomplete form received.';
			    	}
		    	}
		    	
		    	/* Prepare editing form */
		    	
		    	$form->getElement('title')->setValue($request->getParam('title', null));
		    	$news_textarea	= $form->getElement('tinymcetarea');
		    	$news_textarea->setValue($editor_raw_content);
		    	$form->getElement('tags')->setValue($request->getParam('tags', null));
		    	$this->view->form 	=	$form;
		    	$this->view->news_editor =	$tinymce->makeForm($editor_raw_content, 'news-'. genRandomNumString(2) );
		    	
		    	if (!empty($this->_newsErrors))
			    	$this->view->errormsgs = $this->_newsErrors;    
		}
		else
		{
			/* No access */
			$this->_redirect('articles/news' . $news_obj->getLatestUrl());
		}
    	
    }
    
    public function searchAction()
    {
    	
    } 
    
    protected function getNewsPriviledge($news)
    {
    	/* Check if user has the ability to edit page */
    	$user 			= Point_Model_User::getInstance();
    	
    	$news_obj 	= Point_Model_News::getInstance();
    	
    	$news_id 	= null;
    	
    	if (is_array($news) && !empty($news))
    	{
    		$news_id = $news['news_id'];
    	}
	    else if(is_numeric($news))
	    {
		    if ($news_this 	= $news_obj->getArticleById($news, false))
			    $news_id		= $news_this['news_id'];
	    }
	    
	    if (null === $news_id || !is_numeric($news_id))
	    	return Point_Model_User::GROUP_GUEST;
	    	
    	return $this->_user_priviledge = $news_obj->getPriviledge($news_id, $user->getUserId());
    }


    protected function getPriviledge($article)
    {
    	/* Check if user has the ability to edit page */
    	$user 			= Point_Model_User::getInstance();
    	
    	$article_obj 	= Point_Model_Article::getInstance();
    	
    	$article_id 	= null;
    	
    	if (is_array($article) && !empty($article))
    	{
    		$article_id = $article['article_id'];
    	}
	    else if(is_numeric($article))
	    {
		    
		    if ($article_this 	= $article_obj->getArticleById($article, false))
			    $article_id		= $article_this['article_id'];
	    }
	    
	    if (null === $article_id || !is_numeric($article_id))
	    	return Point_Model_User::GROUP_GUEST;
	    	
    	return $this->_user_priviledge = $article_obj->getPriviledge($article_id, $user->getUserId());
    }

	
}
