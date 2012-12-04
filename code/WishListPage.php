<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class WishListPage extends Page {

	/**
	 * Icon to use for this page type.
	 */
	static $icon = "wishlist/images/treeicons/WishListPage";

	/**
	 * Additional page database fields.
	 */
	static $db = array(
		"AddedToListText" => "Varchar(255)",
		"AddedToListTextError" => "Varchar(255)",
		"RemovedFromListConfirmation" => "Varchar(255)",
		"RetrieveListConfirmation" => "Varchar(255)",
		"ClearListConfirmation" => "Varchar(255)",
		"RemovedFromListText" => "Varchar(255)",
		"RemovedFromListTextError" => "Varchar(255)",
		"ClearWishList" => "Varchar(255)",
		"SavedWishListText" => "Varchar(255)",
		"SavedWishListTextError" => "Varchar(255)",
		"RetrievedWishListText" => "Varchar(255)",
		"RetrievedWishListTextError" => "Varchar(255)"
	);


	/**
	 * Add page CMS fields.
	 * @return FieldSet
	 */
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "AddedToListText", $title = "added to list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "AddedToListTextError", $title = "could not add to list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "RemovedFromListText", $title = "removed from list"));
		$fields->addFieldToTab("Root.Content.SaveAndRemoveMessages", new TextField($name = "RemovedFromListTextError", $title = "could not remove from list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "ClearWishList", $title = "cleared list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "SavedWishListText", $title = "saved list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "SavedWishListTextError", $title = "could not save list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "RetrievedWishListText", $title = "retrieved list"));
		$fields->addFieldToTab("Root.Content.WholeListMessages", new TextField($name = "RetrievedWishListTextError", $title = "could not retrieve list"));
		$fields->addFieldToTab("Root.Content.DoubleChecksQuestions", new TextField($name = "RemovedFromListConfirmation", $title = "Are you sure you want to remove this item? Pop-up double-check question..."));
		$fields->addFieldToTab("Root.Content.DoubleChecksQuestions", new TextField($name = "RetrieveListConfirmation", $title = "Are you sure you want to retrieve your saved list? Pop-up double-check question... We ask them because they will loose their currently shown list."));
		$fields->addFieldToTab("Root.Content.DoubleChecksQuestions", new TextField($name = "ClearListConfirmation", $title = "Are you sure you want to clear your saved list? Pop-up double-check question..."));
		return $fields;
	}

	/**
	 * Add default records to database.
	 * Make sure you call parent::requireDefaultRecords().
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = array();
		$page = DataObject::get_one("WishListPage");
		if(!$page) {
			$page = new WishListPage();
			$page->Title = "Wish List";
			$page->MetaTitle = "Wish List";
			$page->URLSegment = "wish-list";
			$page->MenuTitle = "wish list";
		}
		if($page) {
			if(!$page->AddedToListText){$page->AddedToListText = "added to wish list"; $update[] ="updated AddedToListText";}
			if(!$page->AddedToListTextError){$page->AddedToListTextError = "could not add to wish list"; $update[] ="updated AddedToListTextError";}
			if(!$page->RemovedFromListConfirmation){$page->RemovedFromListConfirmation = "are you sure you want to remove it from your wish list?"; $update[] ="updated RemovedFromListConfirmation";}
			if(!$page->RetrieveListConfirmation){$page->RetrieveListConfirmation = "Are you sure you would like to retrieve your saved list?  It will replace your current list.  Do you want to go ahead?"; $update[] ="updated RetrieveListConfirmation";}
			if(!$page->ClearListConfirmation){$page->ClearListConfirmation = "Are you sure you would like to clear your saved list? "; $update[] ="updated ClearListConfirmation";}
			if(!$page->RemovedFromListText){$page->RemovedFromListText = "removed from wish list"; $update[] ="updated RemovedFromListText";}
			if(!$page->RemovedFromListTextError){$page->RemovedFromListTextError = "could not be removed from wish list"; $update[] ="updated RemovedFromListTextError";}
			if(!$page->ClearWishList){$page->ClearWishList = "cleared wish list"; $update[] ="updated ClearWishList";}
			if(!$page->SavedWishListText){$page->SavedWishListText = "saved wish list"; $update[] ="updated SavedWishListText";}
			if(!$page->SavedWishListTextError){$page->SavedWishListTextError = "could not save wish list"; $update[] ="updated SavedWishListTextError";}
			if(!$page->RetrievedWishListText){$page->RetrievedWishListText = "retrieved wish list"; $update[] ="updated RetrievedWishListText";}
			if(!$page->RetrievedWishListTextError){$page->RetrievedWishListTextError = "could not retrieve wish list"; $update[] ="updated RetrievedWishListTextError";}
			if(count($update)) {
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				DB::alteration_message($page->ClassName." created/updated: <ul><li>".implode("</li><li>", $update)."</li></ul>", 'created');
			}
		}
	}


}

class WishListPage_Controller extends Page_Controller {

	/**
	 * Initialisation function that is run before any action on the controller is called.
	 */
	function init() {
		parent::init();
		WishListDecorator_Controller::set_inline_requirements();
	}

	/**
	 * Return whether there are wish list items to be saved.
	 * @return boolean
	 */
	function CanSaveWishList() {
		return $this->CanRetrieveWishList();
	}

	/**
	 * Return whether there are wish list items to be retrieved (that haven't already been retrieved).
	 * @return boolean
	 */
	function CanRetrieveWishList() {
		if($array = WishListDecorator_Controller::get_wish_list_from_member_array()) {
			if(is_array($array)) {
				if(count($array)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return whether the member wish list is non-empty and hence can be cleared.
	 * @return boolean
	 */
	function CanClearWishList() {
		return $this->CanRetrieveWishList();
	}



}
