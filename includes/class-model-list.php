<?php

namespace WPCT_BM;

use Exception;
use WPCT_BM\BookMark;

class BookMarkList
{
    private static $table_name = "bm_lists";

    public $id;
    public $name;
    public $user_id;

    public static function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    public static function create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = self::get_table_name();

        $sql = "CREATE TABLE " . $table_name . " (
id int(11) NOT NULL AUTO_INCREMENT,
user_id bigint(20) UNSIGNED NOT NULL,
name varchar(100) NOT NULL,
PRIMARY KEY  (id)
) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function as_json()
    {
        return self::as_array($this);
    }

    private static function as_array($object)
    {
        $array = [
            'user_id' => (int) $object->user_id,
            'name' => (string) $object->name,
        ];

        if (isset($object->id)) {
            $array['id'] = (int) $object->id;
        }

        return $array;
    }

    public static function get_by_user($user_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE user_id = {$user_id};";

        $result = $wpdb->get_results($sql);
        if (!is_array($result) || sizeof($result) === 0) {
            throw new Exception("Not Found", 404);
        }

        $data = [];
        foreach ($result as $entry) {
            $datum = self::as_array($entry);
            $data[] = new BookMarkList($datum);
        }

        return $data;
    }

    public static function get_by_id($id)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE id = {$id}";

        $result = $wpdb->get_results($sql);
        if (!is_array($result) || sizeof($result) === 0) {
            throw new Exception("Not Found", 404);
        }

        $entry = $result[0];
        $datum = self::as_array($entry);

        return new BookMarkList($datum);
    }

    public static function get_favourite($user_id)
    {
        return self::get_by_name($user_id, 'favourites', true);
    }

    public static function get_by_name($user_id, $name, $create = false)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE user_id = {$user_id}
        AND name = '{$name}';";

        $result = $wpdb->get_results($sql);
        if (!is_array($result) || empty($result)) {
            if (!$create) {
                throw new Exception("Not Found", 404);
            }

            $list = (new BookMarkList([
                'user_id' => $user_id,
                'name' => 'favourites',
            ]))->save();
        } else {
            $list = new BookMarkList((array) $result[0]);
        }

        return $list;
    }

    public static function delete_by_name($user_id, $list_name)
    {
        return self::delete_by_user($user_id, $list_name);
    }

    public static function delete_by_user($user_id, $list_name = null)
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = ['user_id' => (int) $user_id];
        if ($list_name !== null) {
            $where['name'] = (string) $list_name;
        }

        return $wpdb->delete($table_name, $where);
    }

    public function __construct($data)
    {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }

        $this->user_id = (int) $data['user_id'];
        $this->name = (string) $data['name'];

        if (empty($this->user_id)) {
            throw new Exception("Bad Request", 400);
        } elseif (empty($this->name)) {
            throw new Exception("Bad Request", 400);
        }
    }

    public function get_user()
    {
        return get_user_by_id($this->user_id);
    }

    public function get_bookmarks()
    {
        try {
            return BookMark::get_by_list($this->id);
        } catch (Exception $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }

            return [];
        }
    }

    public function has_bookmark($film_id)
    {
        try {
            return BookMark::is_bookmarked($this->user_id, $film_id, $this->id);
        } catch (Exception $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
        }

        return false;
    }

    public function save()
    {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    private function insert()
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $inserteds = $wpdb->insert(
            $table_name,
            [
                'user_id' => $this->user_id,
                'name' => $this->name,
            ]
        );

        if ($inserteds === 0) {
            throw new Exception('Internal Server Error', 500);
        }

        $list = self::get_by_id($wpdb->insert_id);
        do_action('wpct_bm_add_list', $list, true);
        return $list;
    }

    private function update()
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $updateds = $wpdb->update(
            $table_name,
            [
                'user_id' => $this->user_id,
                'name' => $this->name,
            ],
            [
                'id' => $this->id
            ]
        );

        if ($updateds === 0) {
            throw new Exception('Internal Server Error', 500);
        }

        $list = self::get_by_id($this->id);
        do_action('wpct_bm_add_list', $$list, false);
        return $list;
    }

    public function remove()
    {
        global $wpdb;
        $table_name = self::get_table_name();

        return $wpdb->delete(
            $table_name,
            [
                'id' => $this->id,
            ]
        );

        do_action('wpct_bm_drop_list', $this->id);
        return $this;
    }
}
