<?php
namespace ctrl\frontend\portfolio;

use core\http\HTTPRequest;

class PortfolioController extends \core\BackController {
	public function executeIndex(HTTPRequest $request) {
		$projectsManager = $this->managers->getManagerOf('PortfolioProjects');
		$categoriesManager = $this->managers->getManagerOf('PortfolioCategories');
		$portfolioManager = $this->managers->getManagerOf('Portfolio');

		$projects = $projectsManager->getList();
		$categories = $categoriesManager->getList();
		$leading = $portfolioManager->getLeadingItemsData();

		$i = 0;
		foreach($categories as $key => $cat) {
			$categories[$key] = $cat->toArray();
			$categories[$key]['changeRow?'] = ($i % 3 == 0 && $i > 0);
			$i++;
		}

		$leadingProjects = array();

		$leadingProjectsNames = array();
		$i = 0;
		foreach($leading as $leadingItem) {
			if ($leadingItem['item']['place'] != 'index') {
				continue;
			}

			$leadingProjectsNames[] = $leadingItem['item']['name'];

			$leadingItem['pullRight?'] = ($i % 2 == 1);
			$leadingProjects[] = $leadingItem;

			$i++;
		}

		$i = 0;
		foreach($projects as $key => $project) {
			if (in_array($project['name'], $leadingProjectsNames)) {
				unset($projects[$key]);
			} else {
				$projects[$key] = $project->toArray();
				$projects[$key]['changeRow?'] = ($i % 3 == 0 && $i > 0);
				$i++;
			}
		}

		$this->page()->addVar('categories', $categories);
		$this->page()->addVar('leadingProjects', $leadingProjects);
		$this->page()->addVar('otherProjects', array_values($projects));
	}

	public function executeShowCategory(HTTPRequest $request) {
		$this->page()->addVar('title', 'Voir une catégorie');

		$projectsManager = $this->managers->getManagerOf('PortfolioProjects');
		$categoriesManager = $this->managers->getManagerOf('PortfolioCategories');

		$catName = $request->getData('name');

		$category = $categoriesManager->get($catName);
		$projects = $projectsManager->getByCategory($catName);

		$i = 0;
		foreach($projects as $key => $project) {
			$projects[$key] = $project->toArray();
			$projects[$key]['changeRow?'] = ($i % 3 == 0 && $i > 0);
			$i++;
		}

		$this->page()->addVar('title', $category['title']);
		$this->page()->addVar('category', $category);
		$this->page()->addVar('projects', $projects);
	}

	public function executeShowProject(HTTPRequest $request) {
		$this->page()->addVar('title', 'Voir un projet');

		$projectsManager = $this->managers->getManagerOf('PortfolioProjects');
		$galleriesManager = $this->managers->getManagerOf('PortfolioGalleries');

		$projectName = $request->getData('name');

		$project = $projectsManager->get($projectName);
		$gallery = $galleriesManager->getByProject($projectName);

		$i = 0;
		foreach($gallery as $key => $item) {
			$gallery[$key] = $item->toArray();
			$gallery[$key]['link'] = $item->link();
			$gallery[$key]['render'] = $item->render();
			$gallery[$key]['changeRow?'] = ($i % 3 == 0 && $i > 0);
			$i++;
		}

		$this->page()->addVar('title', $project['title']);
		$this->page()->addVar('project', $project);
		$this->page()->addVar('gallery?', (count($gallery) > 0));
		$this->page()->addVar('gallery', $gallery);
	}

	public function executeAbout(HTTPRequest $request) {
		$this->page()->addVar('title', 'À propos');

		$portfolioManager = $this->managers->getManagerOf('Portfolio');

		$aboutTexts = $portfolioManager->getAboutTexts();
		$aboutLinks = $portfolioManager->getAboutLinks();

		$this->page()->addVar('aboutTexts', $aboutTexts);
		$this->page()->addVar('aboutLinks', $aboutLinks);
	}
}