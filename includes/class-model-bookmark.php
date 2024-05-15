<?php

namespace WPCT_BM;

use Exception;
use WPCT_BM\BookMarkList;

class BookMark
{
    private static $table_name = 'bm_bookmarks';

    public $id;
    public $user_id;
    public $list_id;
    public $post_id;

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
id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
user_id bigint(20) NOT NULL,
list_id int(11) UNSIGNED NOT NULL,
post_id bigint(20) UNSIGNED NOT NULL,
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
            'list_id' => (int) $object->list_id,
            'post_id' => (int) $object->post_id
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
            throw new Exception('Not Found', 404);
        }

        $data = [];
        foreach ($result as $entry) {
            $datum = self::as_array($entry);
            $data[] = new BookMark($datum);
        }

        return $data;
    }

    public static function get_by_list($list_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE list_id = {$list_id};";

        $result = $wpdb->get_results($sql);
        if (!is_array($result) || sizeof($result) === 0) {
            throw new Exception("Not Found", 404);
        }

        $data = [];
        foreach ($result as $entry) {
            $datum = self::as_array($entry);
            $data[] = new BookMark($datum);
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
            throw new Exception('Not Found', 404);
        }

        $entry = $result[0];
        $datum = self::as_array($entry);

        return new BookMark($datum);
    }

    public static function is_bookmarked($user_id, $post_id, $list_id = null)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT COUNT(*) count FROM {$table_name}
        WHERE user_id = {$user_id} AND post_id = {$post_id}";
        if ($list_id) {
            $sql .= " AND list_id = {$list_id}";
        }
        $sql .= ';';

        $result = $wpdb->get_results($sql);
        return $result[0]->count > 0;
    }

    public static function get_one($user_id, $post_id, $list_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE user_id = {$user_id} AND post_id = {$post_id}";
        if ($list_id) {
            $sql .= " AND list_id = '$list_id'";
        }

        $result = $wpdb->get_results($sql);
        if (!is_array($result) || sizeof($result) === 0) {
            throw new Exception('Not Found', 404);
        }

        $entry = $result[0];
        $datum = self::as_array($entry);

        return new BookMark($datum);
    }

    public static function delete_by_user($user_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = ['user_id' => (int) $user_id];
        return $wpdb->delete($table_name, $where);
    }

    public static function delete_by_list($list_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = ['list_id' => (int) $list_id];
        return $wpdb->delete($table_name, $where);
    }

    public static function delete_by_post($post_id)
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = ['post_id' => (int) $post_id];
        return $wpdb->delete($table_name, $where);
    }

    public function __construct($data)
    {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }

        $this->user_id = (int) $data['user_id'];
        $this->list_id = (int) $data['list_id'];
        $this->post_id = (int) $data['post_id'];
    }

    public function get_user()
    {
        return get_user_by_id($this->user_id);
    }

    public function get_list()
    {
        return BookMarkList::get_by_id($this->list_id);
    }

    public function get_post()
    {
        return get_post($this->post_id);
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
                'list_id' => $this->list_id,
                'post_id' => $this->post_id
            ]
        );

        if ($inserteds === 0) {
            throw new Exception('Internal Server Error', 500);
        }

        $bookmark = self::get_by_id($wpdb->insert_id);
        do_action('wpct_bm_save_bookmark', $bookmark, true);
        return $bookmark;
    }

    private function update()
    {
        global $wpdb;
        $table_name = self::get_table_name();

        $updateds = $wpdb->update(
            $table_name,
            [
                'user_id' => $this->user_id,
                'list_id' => $this->list_id,
                'post_id' => $this->post_id
            ],
            [
                'id' => $this->id
            ]
        );

        if ($updateds === 0) {
            throw new Exception('Internal Server Error', 500);
        }

        $bookmark = self::get_by_id($wpdb->insert_id);
        do_action('wpct_bm_save_bookmark', $bookmark, false);
        return $bookmark;
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

        do_action('wpct_bm_drop_bookmark', $this->id);
        return $this;
    }
}
