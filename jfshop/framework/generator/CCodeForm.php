<?php
/**
 * CCodeForm class file.
 *
 * @author 
 * @link 
 * @copyright 
 * @license 
 */

/**
 * CCodeForm represents the form for collecting code generation parameters.
 *
 * @author 
 * @package system.gii
 * @since 1.1.2
 */
class CCodeForm extends CActiveForm
{
	/**
	 * @var CCodeModel the code model associated with the form
	 */
	public $model;

	/**
	 * Initializes the widget.
	 * This renders the form open tag.
	 */
	public function init()
	{
		echo <<<EOD
<div class="form gii">
	<p class="note">
		Fields with <span class="required">*</span> are required.
		Click on the <span class="sticky">highlighted fields</span> to edit them.
	</p>
EOD;
		parent::init();
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$templates=array();
		foreach($this->model->getTemplates() as $i=>$template)
			$templates[$i]=basename($template).' ('.$template.')';

		$this->renderFile(Mod::getPathOfAlias('gii.views.common.generator').'.php',array(
			'model'=>$this->model,
			'templates'=>$templates,
		));

		parent::run();

		echo "</div>";
	}
}