<?php
/**
 *
 * @author romain[at]sunnysideup.co.nz
 * @description: adds functions to the shopping cart
 *
 **/

class WishListMemberDecorator extends DataObjectDecorator {

	/**
	 * Define extra database fields for member object.
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				//Wish list will be stored as a serialised array.
				'WishList' => 'Text'
			)
		);
	}

	/**
	 * standard SS function - we dont need to show the Wish List field in the CMS.
	 */
	function updateCMSFields(&$fields) {
		$fields->removeByName("WishList");
		$member = Member::currentUser();
		if($member && $member->IsAdmin()) {
			$html = "";
			$array = unserialize($this->owner->WishList);
			$links = array();
			if(is_array($array) && count($array)) {
				foreach($array as $item) {
					$object = DataObject::get_by_id($item[0], $item[1]);
					if($object) {
						$links[] = "<a href=\"".$object->Link()."\">".$object->Title."</a>";
					}
					else {
						$links[] = "error in retrieving object ".implode(", ", $item);
					}
				}
			}
			else {
				$links[] = "no items on wishlist";
			}
			$html = "<ul><li>".implode("</li><li>", $links)."</li></ul>";
			$field = new LiteralField(
				"WishListOverview",
				$html
			);
			$fields->addFieldToTab("Root.WishList", $field);
		}
		else {
			$fields->removeByName("WishList");
		}
	}



}
