<?php

namespace MN\Filmmarks;

use Exception;

class Model
{
    private static $table_name = "user_filmmarks";

    private $id;
    private $user_id;
    private $list_name;
    private $film_id;

    public static function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . Model::$table_name;
    }

    public static function create_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = Model::get_table_name();

        $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        list_name VARCHAR(100) NOT NULL,
        film_id bigint(20) NOT NULL,
        PRIMARY KEY  (id),
        KEY user_list (user_id, list_name)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function as_array($data)
    {
        $array = [
            'user_id' => (int) $data->user_id,
            'list_name' => (string) $data->list_name,
            'film_id' => (int) $data->film_id
        ];

        if (isset($data['id'])) {
            $array['id'] = (int) $data['id'];
        }

        return $array;
    }

    public static function get_by_user($user_id, $list_name = null)
    {
        global $wpdb;
        $table_name = Model::get_table_name();
        $sql = "SELECT * FROM {$table_name}
        WHERE user_id = {$user_id}";
        if ($list_name !== null) {
            $sql .= " AND list_name = '{$list_name}'";
        }
        $sql . ';';

        $result = $wpdb->get_results($sql);
        if (sizeof($result) === 0) throw new Exception("Not Found", 404);

        $data = [];
        foreach ($result as $entry) {
            $datum = Model::as_array($entry);
            $data[] = new Model($datum);
        }

        return $data;
    }

    public static function get_by_id($film_id)
    {
        global $wpdb;
        $sql = "";
        $result = $wpdb->get_results($sql);
        if (sizeof($result) === 0) throw new Exception("Not Found", 404);
        $entry = $result[0];
        $datum = Model::as_array($entry);

        return new Model($datum);
    }

    public static function delete_by_user($user_id, $list_name = null)
    {
        global $wpdb;
        $table_name = Model::get_table_name();

        $where = ['user_id' => (int) $user_id];
        if ($list_name !== null) {
            $where['list_name'] = (string) $list_name;
        }

        return $wpdb->delete($table_name, $where);
    }

    public function __construct($data)
    {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }

        $this->user_id = (int) $data['user_id'];
        $this->list_name = (string) $data['list_name'];
        $this->film_id = (int) $data['film_id'];
    }

    public function get_user()
    {
        return get_user_by_id($this->user_id);
    }

    public function get_list()
    {
        return get_user_meta($this->list_name);
    }

    public function set_list($list_name)
    {
        $this->list_name = $list_name;
    }

    public function get_film()
    {
        return get_post_by_id($this->film_id);
    }

    public function save()
    {
        global $wpdb;
        $table_name = Model::get_table_name();

        if ($this->id) {
            return $this->update();
        }

        return $wpdb->insert(
            $table_name,
            [
                'user_id' => $this->user_id,
                'list_name' => $this->list_name,
                'film_id' => $this->film_id
            ]
        );
    }

    private function update()
    {
        global $wpdb;
        $table_name = Model::get_table_name();

        return $wpdb->update(
            $table_name,
            [
                'user_id' => $this->user_id,
                'list_name' => $this->list_name,
                'film_id' => $this->film_id
            ],
            [
                'id' => $this->id
            ]
        );
    }

    public function remove()
    {
        global $wpdb;
        $table_name = Model::get_table_name();

        return $wpdb->delete(
            $table_name,
            [
                'id' => $this->id,
            ]
        );
    }
}
