<?php
/**
 * This is the add category-action, it will display a form to create a new category
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class BackendAgendaAddCategory extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('addCategory');
		$this->frm->addText('title');

		$this->meta = new BackendMeta($this->frm, null, 'title', true);
		$this->meta->setURLCallback('BackendAgendaModel', 'getURLForCategory');
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['meta_id'] = $this->meta->save();
				$item['sequence'] = BackendAgendaModel::getMaximumCategorySequence() + 1;

				// save the data
				$item['id'] = BackendAgendaModel::insertCategory($item);
				BackendModel::triggerEvent($this->getModule(), 'after_add_category', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(
					BackendModel::createURLForAction('categories') .
					'&report=added-category&var=' . urlencode($item['title']) .
					'&highlight=row-' . $item['id']
				);
			}
		}
	}
}
