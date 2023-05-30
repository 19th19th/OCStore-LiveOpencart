<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerBlogCategory extends Controller {
	private $error = array();
	private $blog_category_id = 0;
	private $path = array();

	public function index() {
		$this->load->language('blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/category');

		$this->getList();
	}

	public function add() {
		$this->load->language('blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_category->addCategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_category->editCategory($this->request->get['blog_category_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $blog_category_id) {
				$this->model_blog_category->deleteCategory($blog_category_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$url = '';
		
		$this->load->language('blog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/category');

		if ($this->validateRepair()) {
			$this->model_blog_category->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		$url = '';
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('blog/category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('blog/category/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['repair'] = $this->url->link('blog/category/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['enabled'] = $this->url->link('blog/category/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['disabled'] = $this->url->link('blog/category/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['path'])) {
			if ($this->request->get['path'] != '') {
					$this->path = explode('_', $this->request->get['path']);
					$this->blog_category_id = end($this->path);
					$this->session->data['path'] = $this->request->get['path'];
			} else {
				unset($this->session->data['path']);
			}
		} elseif (isset($this->session->data['path'])) {
				$this->path = explode('_', $this->session->data['path']);
				$this->blog_category_id = end($this->path);
 		}
		
		$data['categories'] = $this->getCategories(0);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
		$category_total = $this->model_blog_category->getTotalCategories();

		$data['results'] = $this->language->get('text_category_total') . ($category_total);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/category_list', $data));
	}

	protected function getForm() {

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
		
		if (isset($this->error['meta_h1'])) {
			$data['error_meta_h1'] = $this->error['meta_h1'];
		} else {
			$data['error_meta_h1'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['blog_category_id'])) {
			$data['action'] = $this->url->link('blog/category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('blog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&blog_category_id=' . $this->request->get['blog_category_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['blog_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_blog_category->getCategory($this->request->get['blog_category_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['category_description'])) {
			$data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['blog_category_id'])) {
			$data['category_description'] = $this->model_blog_category->getCategoryDescriptions($this->request->get['blog_category_id']);
		} else {
			$data['category_description'] = array();
		}
		
		$language_id = $this->config->get('config_language_id');
		if (isset($data['category_description'][$language_id]['name'])) {
			$data['heading_title'] = $data['category_description'][$language_id]['name'];
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['category_store'])) {
			$data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($this->request->get['blog_category_id'])) {
			$data['category_store'] = $this->model_blog_category->getCategoryStores($this->request->get['blog_category_id']);
		} else {
			$data['category_store'] = array(0);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($category_info)) {
			$data['top'] = $category_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($category_info)) {
			$data['column'] = $category_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}
		
		if (isset($this->request->post['category_seo_url'])) {
			$data['category_seo_url'] = $this->request->post['category_seo_url'];
		} elseif (isset($this->request->get['blog_category_id'])) {
			$data['category_seo_url'] = $this->model_blog_category->getCategorySeoUrls($this->request->get['blog_category_id']);
		} else {
			$data['category_seo_url'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($category_info)) {
			$data['noindex'] = $category_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['category_layout'])) {
			$data['category_layout'] = $this->request->post['category_layout'];
		} elseif (isset($this->request->get['blog_category_id'])) {
			$data['category_layout'] = $this->model_blog_category->getCategoryLayouts($this->request->get['blog_category_id']);
		} else {
			$data['category_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/category_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'blog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 0) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
			
			if ((utf8_strlen($value['meta_h1']) < 0) || (utf8_strlen($value['meta_h1']) > 255)) {
				$this->error['meta_h1'][$language_id] = $this->language->get('error_meta_h1');
			}
		}

		if ($this->request->post['category_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
	
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['blog_category_id']) || ($seo_url['query'] != 'blog_category_id=' . $this->request->get['blog_category_id']))) {		
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
				
								break;
							}
						}
					}
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
	
	public function enable() {
        $this->load->language('blog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('blog/category');

        if (isset($this->request->post['selected'])) {

            foreach ($this->request->post['selected'] as $blog_category_id) {
                $this->model_blog_category->editCategoryStatus($blog_category_id, 1);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function disable() {
        $this->load->language('blog/category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('blog/category');

        if (isset($this->request->post['selected'])) {

            foreach ($this->request->post['selected'] as $blog_category_id) {
                $this->model_blog_category->editCategoryStatus($blog_category_id, 0);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->response->redirect($this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'blog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'blog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('blog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => $this->config->get('config_limit_autocomplete')
			);

			$results = $this->model_blog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'blog_category_id' => $result['blog_category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function getCategories($parent_id, $parent_path = '', $indent = '') {
		$blog_category_id = array_shift($this->path);
		$output = array();
		static $href_category = null;
		static $href_action = null;
		if ($href_category === null) {
			$href_category = $this->url->link('blog/category', 'user_token=' . $this->session->data['user_token'] . '&path=', true);
			$href_action = $this->url->link('blog/category/update', 'user_token=' . $this->session->data['user_token'] . '&blog_category_id=', true);
		}
		$results = $this->model_blog_category->getCategoriesByParentId($parent_id);
		foreach ($results as $result) {
			$path = $parent_path . $result['blog_category_id'];
			$href = ($result['children']) ? $href_category . $path : '';
			$name = $result['name'];
			if ($blog_category_id == $result['blog_category_id']) {
				$name = '<b>' . $name . '</b>';
				$data['breadcrumbs'][] = array(
					'text'      => $result['name'],
					'href'      => $href,
					'separator' => ' :: '
			);
				$href = '';
			}
			$selected = isset($this->request->post['selected']) && in_array($result['blog_category_id'], $this->request->post['selected']);
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $href_action . $result['blog_category_id']
			);
			$output[$result['blog_category_id']] = array(
				'blog_category_id' => $result['blog_category_id'],
				'name'        => $name,
				'sort_order'  => $result['sort_order'],
				'noindex'  	  => $result['noindex'],
				'edit'        => $this->url->link('blog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&blog_category_id=' . $result['blog_category_id'], true),
				'selected'    => $selected,
				'action'      => $action,
				'href'        => $href,
				'href_shop'   => HTTP_CATALOG . 'index.php?route=blog/category&blog_category_id=' . ($result['blog_category_id']),
				'indent'      => $indent
			);
			if ($blog_category_id == $result['blog_category_id']) {
				$output += $this->getCategories($result['blog_category_id'], $path . '_', $indent . str_repeat('&nbsp;', 8));
			}
		}
		return $output;
	}
	private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
		$output = array();
		if (array_key_exists($parent_id, $categories)) {
			if ($parent_name != '') {
				$parent_name .= $this->language->get('text_separator');
			}
			foreach ($categories[$parent_id] as $category) {
				$output[$category['blog_category_id']] = array(
					'blog_category_id' => $category['blog_category_id'],
					'name'        => $parent_name . $category['name']
				);
				$output += $this->getAllCategories($categories, $category['blog_category_id'], $parent_name . $category['name']);
			}
		}
		return $output;
		}
}