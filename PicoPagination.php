<?php

class PicoPagination extends AbstractPicoPlugin
{
	const API_VERSION = 2;

	protected $enabled = false;

	public $config = array();
	public $offset = 0;
	public $page_number = 0;
	public $total_pages = 1;
	public $paged_pages = array();

	public function __construct(Pico $pico)
	{
		parent::__construct($pico);

		$this->config = array(
			'limit' => 5,
			// 'next_text' => 'Next &gt;',
			// 'prev_text' => '&lt; Previous',
			'page_indicator' => 'page',
			// 'output_format'	=> 'links',
			// 'flip_links' => false,
			// 'filter_date' => true,
			'sub_page' => false,
			// 'order' => 'asc',
		);
	}

	public function onConfigLoaded(&$settings)
	{
		// if (isset($settings['pages_order']))
		// 	$this->config['order'] = strtolower($settings['pages_order']);

		// Pull config options for site config
		if (isset($settings['pagination_limit']))
			$this->config['limit'] = $settings['pagination_limit'];
		
		// if (isset($settings['pagination_next_text']))
		// 	$this->config['next_text'] = $settings['pagination_next_text'];
		// if (isset($settings['pagination_prev_text']))
		// 	$this->config['prev_text'] = $settings['pagination_prev_text'];
		// if (isset($settings['pagination_flip_links']))
		// 	$this->config['flip_links'] = $settings['pagination_flip_links'];
		// if (isset($settings['pagination_filter_date']))
		// 	$this->config['filter_date'] = $settings['pagination_filter_date'];
		if (isset($settings['pagination_page_indicator']))
			$this->config['page_indicator'] = $settings['pagination_page_indicator'];
		// if (isset($settings['pagination_output_format']))
		// 	$this->config['output_format'] = $settings['pagination_output_format'];
		if (isset($settings['pagination_sub_page']))
			$this->config['sub_page'] = $settings['pagination_sub_page'];

		// TODO create next/previous urls
	}

	// public function onPagesLoaded(&$pages)
	// {
	// 	// if filter_date is true, it filters so only dated items are returned.
	// 	if ($this->config['filter_date']) {
	// 		$show_pages = array();

	// 		foreach($pages as $key=>$page) {
	// 			if ($page['date']) {
	// 				$show_pages[$key] = $page;
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		$show_pages = $pages;
	// 	}
		
	// 	// get total pages before show_pages is sliced
	// 	$this->total_pages = floor(count($show_pages) / $this->config['limit']);
	// 	// set filtered pages to paged_pages
	// 	$this->paged_pages = $this->paginate($show_pages);
	// }

	public function onPageRendering(&$templateName, &$twigVariables)
	{
		// Override 404 header
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');

		// TODO create next/previous urls

		// Set a bunch of view vars

		// send the paged pages in separate var
		// if ($this->paged_pages)
		// 	$twigVariables['paged_pages'] = $this->paged_pages;

		// set var for page_number
		if ($this->page_number)
			$twigVariables['page_number'] = $this->page_number;

		// set var for total pages
		if ($this->total_pages)
			$twigVariables['total_pages'] = $this->total_pages;

		// set var for page_indicator
		$twigVariables['page_indicator'] = $this->config['page_indicator'];
		
		// // build pagination links
		// // set next and back link vars to empty. links will be added below if they are available.
		// $twigVariables['next_page_link'] = $twigVariables['prev_page_link'] = '';
		// $pagination_parts = array();
		// if ($this->page_number > 1) {
		// 	$prev_path = $this->getBaseUrl() . '/' . $this->config['page_indicator'] . '/' . ($this->page_number - 1);
		// 	$pagination_parts['prev_link'] = $twigVariables['prev_page_link'] = '<a href="' . $prev_path . '" id="prev_page_link">' . $this->config['prev_text'] . '</a>';
		// }
		// if ($this->page_number < $this->total_pages) {
		// 	$next_path = $this->getBaseUrl() . '/' . $this->config['page_indicator'] . '/' . ($this->page_number + 1);
		// 	$pagination_parts['next_link'] = $twigVariables['next_page_link'] = '<a href="' . $next_path . '" id="next_page_link">' . $this->config['next_text'] . '</a>';
		// }

		// // reverse order if flip_links is on
		// if ($this->config['flip_links']) {
		// 	$pagination_parts = array_reverse($pagination_parts);
		// }

		// // create pagination links output
		// if ($this->config['output_format'] == "list") {
        //     $twigVariables['pagination_links'] = '<ul id="pagination"><li>' . implode('</li><li>', array_values($pagination_parts)) . '</li></ul>';
		// } else {
        //     $twigVariables['pagination_links'] = implode(' ', array_values($pagination_parts));
		// }

		// set page of page var
        // $twigVariables['page_of_page'] = "Page " . $this->page_number . " of " . $this->total_pages . ".";
	}

	public function onTwigRegistered(Twig_Environment &$twig)
	{
		// $twig->addFilter(new \Twig_SimpleFilter('paginate', array($this, 'paginkate')));
		$twig->addFilter(new Twig_SimpleFilter('paginate', [$this, 'paginate']));
		$twig->addFilter(new Twig_SimpleFilter('total_pages', [$this, 'total_pages']));

		$twig->addFilter(new Twig_SimpleFilter('next_page', [$this, 'next_page']));
		$twig->addFilter(new Twig_SimpleFilter('previous_page', [$this, 'previous_page']));

		$twig->addFilter(new Twig_SimpleFilter('filter_hidden', [$this, 'filter_hidden']));
		$twig->addFilter(new Twig_SimpleFilter('filter_folders', [$this, 'filter_folders'], ['is_variadic' => true]));
		$twig->addFilter(new Twig_SimpleFilter('filter_ids', [$this, 'filter_ids'], ['is_variadic' => true]));
		$twig->addFilter(new Twig_SimpleFilter('filter_dated', [$this, 'filter_dated']));

		$twig->addFilter(new Twig_SimpleFilter('only_hidden', [$this, 'only_hidden']));
		$twig->addFilter(new Twig_SimpleFilter('only_folders', [$this, 'only_folders'], ['is_variadic' => true]));
		$twig->addFilter(new Twig_SimpleFilter('only_ids', [$this, 'only_ids'], ['is_variadic' => true]));
		$twig->addFilter(new Twig_SimpleFilter('only_dated', [$this, 'only_dated']));
	}

	public function paginate($pages)
	{
		return array_slice($pages, $this->offset, $this->config['limit']);
	}

	public function total_pages($pages)
	{
		return floor(count($pages) / $this->config['limit']);
	}

	public function filter_hidden($pages)
	{
		return array_filter($pages, function ($page) {
			return ! boolval($page['hidden']);
		});
	}

	public function only_hidden($pages)
	{
		return array_filter($pages, function ($page) {
			return boolval($page['hidden']);
		});
	}

	public function filter_folders($pages, array $folders = [])
	{
		if (! empty($folders) && is_array($folders[0])) {
			$folders = $folders[0];
		}

		$folders = array_map(function ($folder) {
			return rtrim($folder, '/').'/';
		}, $folders);

		return array_filter($pages, function ($page) use ($folders) {
			foreach ($folders as $folder) {
				if (self::startsWith($page['id'], $folder)) {
					return false;
				}
			}
			
			return true;
		});
	}

	public function only_folders($pages, array $folders = [])
	{
		if (! empty($folders) && is_array($folders[0])) {
			$folders = $folders[0];
		}

		$folders = array_map(function ($folder) {
			return rtrim($folder, '/').'/';
		}, $folders);

		return array_filter($pages, function ($page) use ($folders) {
			foreach ($folders as $folder) {
				if (self::startsWith($page['id'], $folder)) {
					return true;
				}
			}
			
			return false;
		});
	}

	public function filter_ids($pages, array $ids = [])
	{
		if (! empty($ids) && is_array($ids[0])) {
			$ids = $ids[0];
		}

		return array_filter($pages, function ($page) use ($ids) {
			foreach ($ids as $id) {
				if ($page['id'] == $id) {
					return false;
				}
			}
			
			return true;
		});
	}

	public function only_ids($pages, array $ids = [])
	{
		if (! empty($ids) && is_array($ids[0])) {
			$ids = $ids[0];
		}

		return array_filter($pages, function ($page) use ($ids) {
			foreach ($ids as $id) {
				if ($page['id'] == $id) {
					return true;
				}
			}
			
			return false;
		});
	}

	public function filter_dated($pages)
	{
		return array_filter($pages, function ($page) {
			return empty($page['date']);
		});
	}

	public function only_dated($pages)
	{
		return array_filter($pages, function ($page) {
			return ! empty($page['date']);
		});
	}

	public function next_page($pages)
	{
		$pico = $this->getPico();
		$currentPage = $pico->getCurrentPage();
		$indexMap = array_keys($pages);
		$keyMap = array_flip($indexMap);

		$index = $keyMap[$currentPage['id']];
		$last = count($pages) - 1;

		if ($index < $last) {
			return $pages[$indexMap[$index + 1]];
		}

		return null;
	}

	public function previous_page($pages)
	{
		$pico = $this->getPico();
		$currentPage = $pico->getCurrentPage();
		$indexMap = array_keys($pages);
		$keyMap = array_flip($indexMap);

		$index = $keyMap[$currentPage['id']];

		if ($index > 0) {
			return $pages[$indexMap[$index - 1]];
		}

		return null;
	}

	public function onRequestUrl(&$url)
	{
		// checks for page # in URL
		$pattern = '/' . $this->config['page_indicator'] . '\/[0-9]*$/';
		if (preg_match($pattern, $url)) {
			$page_numbers = explode('/', $url);
			$page_number = $page_numbers[count($page_numbers)-1];
			$this->page_number = $page_number;
			if ($this->config['sub_page']) {
				$url = $this->config['page_indicator'];
			} else {
				$url = preg_replace($pattern, '', $url);
			}
		} else {
			$this->page_number = 1;
		}

		$this->offset = ($this->page_number-1) * $this->config['limit'];
	}

	protected static function startsWith($string, $startString)
	{ 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	}
}
