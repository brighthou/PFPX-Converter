<?php


namespace lianghou\PFPXConverter;


use mysqli;

class pfpx2mysql
{
    /**
     * @var string $dbServer 数据库所属服务器的IP地址（含端口）
     */
    public $dbServer;

    /**
     * @var string $dbUserName 数据库用户名
     */
    public $dbUserName;

    /**
     * @var string $dbPassword 数据库密码
     */
    public $dbPassword;

    /**
     * @var string $dbName 数据库名称
     */
    public $dbName;

    /**
     * @var string $tablePrefix 数据表名称前缀
     */
    public $tablePrefix;

    /**
     * @var string $navFilePath PFPX导航数据文件路径
     */
    public $navFilePath;

    /**
     * pfpx2mysql constructor.
     */
    public function __construct()
    {
    }

    /**
     * 创建用于记录PFPX导航数据的数据库结构
     *
     * @return void
     */
    public function createDatabase()
    {
        // 判断成员变量是否填写
        if ($this->dbName == NULL) {
            $this->dbName = 'pfpx';
        }

        if ($this->tablePrefix == NULL) {
            $this->tablePrefix = 'public';
        }

        // 连接数据库
        $con = $this->connectDatabase();

        $dbName = $this->dbName;
        $tablePrefix = $this->tablePrefix;

        // 创建数据库
        $sql = "CREATE DATABASE IF NOT EXISTS `".$dbName."` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("数据库创建失败: " . $con->error);
        }

        // 创建数据库结构
        $sql = "USE `".$dbName."`;";
        if ($con->query($sql) === TRUE) {} else {
            die("调用数据库 ".$dbName."失败: " . $con->error);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".$tablePrefix."_airway` (
  `id` int NOT NULL AUTO_INCREMENT,
  `awy_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cruise_table` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `awy_type` tinyint DEFAULT NULL,
  `start_id` int DEFAULT NULL,
  `start_wpt` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `end_id` int DEFAULT NULL,
  `end_wpt` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_two_way` tinyint DEFAULT NULL,
  `start_lat` decimal(10,8) DEFAULT NULL,
  `start_lon` decimal(11,8) DEFAULT NULL,
  `end_lat` decimal(10,8) DEFAULT NULL,
  `end_lon` decimal(11,8) DEFAULT NULL,
  `lower_limit` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `upper_limit` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_rnav` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `awy_code` (`awy_code`),
  KEY `awy_type` (`awy_type`),
  KEY `start_id` (`start_id`),
  KEY `end_id` (`end_id`),
  KEY `start_wpt` (`start_wpt`),
  KEY `end_wpt` (`end_wpt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("创建数据表 ".$dbName.".".$tablePrefix."_airway 失败: " . $con->error);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".$tablePrefix."_runway` (
  `id` int NOT NULL AUTO_INCREMENT,
  `airport_icao` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `runway_name` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `runway_length` int DEFAULT NULL,
  `runway_width` int DEFAULT NULL,
  `runway_heading` int DEFAULT NULL,
  `runway_lat` decimal(10,8) DEFAULT NULL,
  `runway_lon` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `airport_icao` (`airport_icao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("创建数据表 ".$dbName.".".$tablePrefix."_runway 失败: " . $con->error);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".$tablePrefix."_sid` (
  `id` int NOT NULL AUTO_INCREMENT,
  `airport_icao` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `runway_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sid_id` int DEFAULT NULL,
  `wpt_id` int DEFAULT NULL,
  `wpt_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sid_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sid_trans` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_lat` decimal(10,8) DEFAULT NULL,
  `wpt_lon` decimal(11,8) DEFAULT NULL,
  `is_rnav` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `airport_icao` (`airport_icao`),
  KEY `runway_code` (`runway_code`),
  KEY `sid_id` (`sid_id`),
  KEY `wpt_id` (`wpt_id`),
  KEY `wpt_code` (`wpt_code`),
  KEY `sid_code` (`sid_code`),
  KEY `sid_trans` (`sid_trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("创建数据表 ".$dbName.".".$tablePrefix."_sid 失败: " . $con->error);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".$tablePrefix."_star` (
  `id` int NOT NULL AUTO_INCREMENT,
  `airport_icao` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `runway_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `star_id` int DEFAULT NULL,
  `wpt_id` int DEFAULT NULL,
  `wpt_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `star_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `star_trans` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_lat` decimal(10,8) DEFAULT NULL,
  `wpt_lon` decimal(11,8) DEFAULT NULL,
  `is_rnav` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `airport_icao` (`airport_icao`),
  KEY `runway_code` (`runway_code`),
  KEY `star_id` (`star_id`),
  KEY `wpt_id` (`wpt_id`),
  KEY `wpt_code` (`wpt_code`),
  KEY `star_code` (`star_code`),
  KEY `star_trans` (`star_trans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("创建数据表 ".$dbName.".".$tablePrefix."_star 失败: " . $con->error);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".$tablePrefix."_waypoint` (
  `id` int NOT NULL AUTO_INCREMENT,
  `wpt_code` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `airport_iata` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_type` tinyint DEFAULT NULL,
  `wpt_note` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_airport_wpt` tinyint DEFAULT NULL,
  `airport_type` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_code` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_id` int DEFAULT NULL,
  `freq` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wpt_lat` decimal(10,8) DEFAULT NULL,
  `wpt_lon` decimal(11,8) DEFAULT NULL,
  `wpt_elev` int DEFAULT NULL,
  `rwy_length` int DEFAULT NULL,
  `fir` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uir` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `trans_alt` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wpt_code` (`wpt_code`),
  KEY `airport_iata` (`airport_iata`),
  KEY `wpt_type` (`wpt_type`),
  KEY `is_airport_wpt` (`is_airport_wpt`),
  KEY `airport_type` (`airport_type`),
  KEY `country_code` (`country_code`),
  KEY `wpt_name` (`wpt_name`),
  KEY `wpt_id` (`wpt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        if ($con->query($sql) === TRUE) {} else {
            die("创建数据表 ".$dbName.".".$tablePrefix."_waypoint 失败: " . $con->error);
        }

        // 关闭数据库连接
        $this->closeDatabase($con);
    }

    /**
     * 删除指定的数据库
     *
     * @param $dbName string 数据库名称
     */
    function dropDatabase($dbName)
    {
        // 连接数据库
        $con = $this->connectDatabase();

        // 删除数据库
        $sql = "DROP DATABASE `".$dbName."`;";
        if ($con->query($sql) === TRUE) {} else {
            die("删除数据库失败: " . $con->error);
        }

        $this->closeDatabase($con);
    }

    /**
     * 连接MySQL数据库
     *
     * @return mysqli MySQL连接对象，通常是$con
     */
    private function connectDatabase()
    {
        // 判断连接服务器所需的成员变量是否已指定
        if ($this->dbServer == NULL) {
            die('数据库连接失败：数据库地址未指定，请检查成员属性是否定义');
        }

        if ($this->dbUserName == NULL) {
            die('数据库连接失败：数据库用户名未指定，请检查成员属性是否定义');
        }

        if ($this->dbPassword == NULL) {
            die('数据库连接失败：数据库登录密码未指定，请检查成员属性是否定义');
        }

        $dbServer = $this->dbServer;
        $dbUserName = $this->dbUserName;
        $dbPassword = $this->dbPassword;

        // 创建连接
        $con = new mysqli($dbServer, $dbUserName, $dbPassword);

        // 检测连接
        if ($con->connect_error) {
            die("数据库连接失败: " . $con->connect_error);
        }

        // 初始化数据库选项
        $sql = "SET NAMES 'UTF8'";
        if ($con->query($sql) === TRUE) {} else {
            die("设置数据库编码失败: " . $con->error);
        }

        $sql = "SET time_zone = '+8:00'";
        if ($con->query($sql) === TRUE) {} else {
            die("设置数据库时区(UTC+8)失败: " . $con->error);
        }

        return $con;
    }

    /**
     * 关闭MySQL数据库连接
     *
     * @param mysqli $con MySQL连接对象
     * @return void
     */
    private function closeDatabase($con)
    {
        $con->close();
    }

}