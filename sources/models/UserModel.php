<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{

    public function findUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ' . $id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword)
    {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %' . $keyword . '%' . ' OR user_email LIKE %' . $keyword . '%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? LIMIT 1");
        $stmt->bind_param('s', $userName);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            return [$user];
        }
        return false;
    }



    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id)
    {
        $sql = 'DELETE FROM users WHERE id = ' . $id;
        return $this->delete($sql);
    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input)
    {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) . '", 
                 password="' . md5($input['password']) . '"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    // Using mysqli (self::$_connection is mysqli)
    public function insertUser($input)
    {
        $conn = self::$_connection;
        $stmt = $conn->prepare("INSERT INTO users (`name`, `password`, `fullname`, `type`) VALUES (?, ?, ?, ?)");
        $pw = password_hash($input['password'], PASSWORD_BCRYPT);
        $name = $input['name'] ?? '';
        $fullname = $input['fullname'] ?? '';
        $type = $input['type'] ?? 'user';
        $stmt->bind_param('ssss', $name, $pw, $fullname, $type);
        $stmt->execute();
        $id = $conn->insert_id;
        $stmt->close();
        return $id;
    }


    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = [])
    {
        $conn = self::$_connection;

        // xử lý keyword tìm kiếm
        $keywordSql = "";
        $bind = [];
        if (!empty($params['keyword'])) {
            $keywordSql = " WHERE name LIKE ? OR fullname LIKE ?";
            $kw = "%" . $params['keyword'] . "%";
            $bind = [$kw, $kw];
        }

        // xử lý sort (whitelist)
        $allowed = ['id', 'name', 'fullname'];
        $sort = $params['sort'] ?? 'id';
        $sort = in_array($sort, $allowed) ? $sort : 'id';

        $sql = "SELECT id, name, fullname, type FROM users" . $keywordSql . " ORDER BY $sort";

        if (!empty($bind)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', ...$bind);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } else {
            $res = $conn->query($sql);
            return $res->fetch_all(MYSQLI_ASSOC);
        }
    }
}
