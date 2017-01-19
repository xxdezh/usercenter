<?php
/**
 * This file contains CTypedList class.
 *
 * @author 
 * @link 
 * @copyright 
 * @license 
 */

/**
 * CTypedList represents a list whose items are of the certain type.
 *
 * CTypedList extends {@link CList} by making sure that the elements to be
 * added to the list is of certain class type.
 *
 * @author 
 * @version 
 * @package system.collections
 * @since 1.0
 */
class CTypedList extends CList
{
	private $_type;

	/**
	 * Constructor.
	 * @param string $type class type
	 */
	public function __construct($type)
	{
		$this->_type=$type;
	}

	/**
	 * Inserts an item at the specified position.
	 * This method overrides the parent implementation by
	 * checking the item to be inserted is of certain type.
	 * @param integer $index the specified position.
	 * @param mixed $item new item
	 * @throws CException If the index specified exceeds the bound,
	 * the list is read-only or the element is not of the expected type.
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof $this->_type)
			parent::insertAt($index,$item);
		else
			throw new CException(Mod::t('mod','CTypedList<{type}> can only hold objects of {type} class.',
				array('{type}'=>$this->_type)));
	}
}