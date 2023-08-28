<?php

namespace gtc_core;

use stdClass;

class AffiliatesFilter
{
    public function affiliateTo_list($id_user, $role_name, $affiliates_full_list = [], $afiliate_level = 0)
    {
        $result = new stdClass();
        $affiliates_filter_list = [];

        $affiliate_user = new classaffiliates();
        $affiliate_user = $affiliate_user->selectBy('linked_user', $id_user);
        $affiliate_user = $affiliate_user ? $affiliate_user[0] : null;

        if ($affiliate_user) {
            $affiliates_filter_list = Functions::get_items_by($affiliates_full_list, 'affiliate_to', $affiliate_user->id_affiliates);
            if (((int) $afiliate_level - 1) == $affiliate_user->afiliate_level) {
                array_push($affiliates_filter_list, $affiliate_user);
            }
        }

        if ($affiliate_user) {
            $result->affiliate_user = $affiliate_user;
            $result->affiliates_filter_list = $affiliates_filter_list;
            return $result;
        } else {
            return false;
        }
    }

    public function affiliate_tree_list($id_user, $role_name, $affiliates_full_list = [])
    {
        $result = new stdClass();
        $affiliates_list = [];
        $affiliate_user = [];

        $affiliate_user = new classaffiliates();
        $affiliate_user = $affiliate_user->selectBy('linked_user', $id_user);
        $affiliate_user = $affiliate_user ? $affiliate_user[0] : null;

        if ($affiliate_user) {
            if (is_array($affiliates_full_list)) {
                if ($role_name != 'Affiliate') {
                    $affiliates_list = array_merge($affiliates_list, Functions::get_items_by($affiliates_full_list, 'affiliate_to', $affiliate_user->id_affiliates));
                    if ($affiliates_list) {
                        foreach ($affiliates_list as $item) {
                            $affiliates_list = array_merge($affiliates_list, Functions::get_items_by($affiliates_full_list, 'affiliate_to', $item->id_affiliates));
                        }
                    }
                } else {
                    $affiliates_list = [];
                }
            }
        }

        if ($affiliate_user) {
            $result->affiliate_user = $affiliate_user;
            $result->affiliates_list = $affiliates_list;
            return $result;
        } else {
            return false;
        }
    }

    public function affiliate_level_list($id_user, $role_name)
    {
        $result = new stdClass();
        $affiliate_level = [];
        $affiliate_level_full = new classaffiliate_level();
        $affiliate_level_full = $affiliate_level_full->selectAll();

        if ($role_name == 'MGA') {
            $affiliate_level = array_merge($affiliate_level, Functions::get_items_by($affiliate_level_full, 'description', 'GA'));
            $affiliate_level = array_merge($affiliate_level, Functions::get_items_by($affiliate_level_full, 'description', 'Affiliate'));
        }

        if ($role_name == 'GA') {
            $affiliate_level = array_merge($affiliate_level, Functions::get_items_by($affiliate_level_full, 'description', 'Affiliate'));
        }

        if ($role_name == 'Affiliate') {
            $affiliate_level = [];
        }

        $result->affiliate_level = $affiliate_level;
        return $result;
    }
}

class SurgicalCoordinatorsFilter
{

    public function surgical_coordinators_group_list($id_user, $role_name, $surgical_coordinators_list_full)
    {
        $result = new stdClass();
        $groups_filter_list = [];

        $coordinator_user = new classsurgical_coordinators();
        $coordinator_user = $coordinator_user->selectBy('linked_user', $id_user);
        $coordinator_user = $coordinator_user ? $coordinator_user[0] : null;
        
        if ($coordinator_user) {
            $groups_filter_list_full = new classgroups();
            $groups_filter_list_full = $groups_filter_list_full->selectAll();
            $groups_filter_list = array_merge($groups_filter_list, Functions::get_items_by($groups_filter_list_full, 'id_groups', $coordinator_user->group_detail));
        } 

        if ($coordinator_user) {
            $result->coordinator_user = $coordinator_user;
            $result->groups_filter_list = $groups_filter_list;
            return $result;
        } else {
            return false;
        }
    }
    public function surgical_coordinators_byGroup($id_user, $role_name, $surgical_coordinators_list_full, $group_id)
    {
        $result = new stdClass();
        $surgical_coordinators_list = [];

        // if (in_array($_SESSION['role_name'], ['MGA', 'GA', 'Affiliate'])) {

        // }

        // if (in_array($_SESSION['role_name'], ['Coordinator', 'Group Manager'])) {
        //     $coordinator_user = new classsurgical_coordinators();
        //     $coordinator_user = $coordinator_user->selectBy('linked_user', $id_user);
        //     $coordinator_user = $coordinator_user ? $coordinator_user[0] : null;

        //     if ($coordinator_user) {
        //         $groups_filter_list = (new classgroups())->selectBy('id_groups', $group_id);
        //     }
        // }

        $groups_filter_list = (new classgroups())->selectBy('id_groups', $group_id);

        if ($groups_filter_list) {
            $surgical_coordinators_list = [];
            foreach ($surgical_coordinators_list_full as $item) {
                $surgical_coordinators_list_exist = false;
                $surgical_coordinators_item_groups_list = new classsurgical_coordinators();
                $groups_filter_list_full = new classgroups();
                $surgical_coordinators_item_groups_list =$groups_filter_list_full->selectBy("id_groups", $item->group_detail);
                if ($surgical_coordinators_item_groups_list) {
                    foreach ($surgical_coordinators_item_groups_list as $item_group) {
                        if (in_array($item_group->id_groups, array_column($groups_filter_list, 'id_groups'))) {
                            $surgical_coordinators_list_exist = true;
                            break;
                        }
                    }
                }
                if ($surgical_coordinators_list_exist) {
                    $surgical_coordinators_list_exist = false;
                    array_push($surgical_coordinators_list, $item);
                }
            }
        }

        if ($groups_filter_list) {
            $result->surgical_coordinators_list = $surgical_coordinators_list;
            return $result;
        } else {
            return false;
        }
    }
}

class CustomSecFilters
{
    public $AffiliatesFilter;
    public $SurgicalCoordinatorsFilter;

    public function __construct()
    {
        $this->AffiliatesFilter = new AffiliatesFilter();
        $this->SurgicalCoordinatorsFilter = new SurgicalCoordinatorsFilter();
    }
}


?>