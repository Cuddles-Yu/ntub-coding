############################################## 資料庫核心 ##############################################
# (pip install mysql-connector-python) 安裝第三方模組來連結
import mysql.connector
from mysql.connector import Error
from 地圖資訊爬蟲.crawler.module.const import *

def connect(use_database):  # 連接資料庫
    try:
        if use_database:
            connection = mysql.connector.connect(
                user=USERNAME,
                password=PASSWORD,
                host='localhost',
                database=NAME,
                auth_plugin='mysql_native_password'
            )
        else:
            connection = mysql.connector.connect(
                user=USERNAME,
                password=PASSWORD,
                host='localhost',
                auth_plugin='mysql_native_password'
            )
        if connection.is_connected():
            return connection
    except Error as e:
        print('資料庫連線失敗，請確認服務是否啟用後再嘗試一次')
        return None

# 檢查資料庫是否存在
def exists(c, database_name: str) -> bool:
    c.execute("SHOW DATABASES")  # 查詢獲取所有資料庫的列表
    databases = [db[0] for db in c.fetchall()]
    # 查看資料庫列表，檢查是否存在目標資料庫名稱
    for db in databases:
        if db == database_name:
            return True
    return False

# 建立'管理者'資料表
def _create_administrators_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `administrators` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email_UNIQUE` (`email`)
        )
    ''')

# 建立'貢獻者'資料表
def _create_contributors_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `contributors` (
            `id` varchar(255) NOT NULL,
            `level` int NOT NULL,
            PRIMARY KEY (`id`)
        )
    ''')

# 建立'標籤'資料表
def _create_tags_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `tags` (
            `tag` varchar(20) NOT NULL,
            `category` varchar(20) DEFAULT NULL,
            PRIMARY KEY (`tag`)
        )
    ''')

# 建立'會員'資料表
def _create_members_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `members` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `username` varchar(255) NOT NULL,
            `password` varchar(255) DEFAULT NULL,
            `profile_picture` varchar(255) DEFAULT NULL,
            `popular_weight` int DEFAULT '1',
            `environment_weight` int DEFAULT '1',
            `price_weight` int DEFAULT '1',
            `product_weight` int DEFAULT '1',
            `service_weight` int DEFAULT '1',
            `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email_UNIQUE` (`email`)
        )
    ''')

# 建立'商家'資料表
def _create_stores_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `stores` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `tag` varchar(20) DEFAULT NULL,
            `preview_image` varchar(255) DEFAULT NULL,
            `link` varchar(2000) NOT NULL,
            `website` varchar(2000) DEFAULT NULL,
            `phone_number` varchar(30) DEFAULT NULL,
            `last_update` varchar(20) DEFAULT NULL,
            `crawler_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_UNIQUE` (`name`),
            KEY `fk_tag_s_idx` (`tag`),
            CONSTRAINT `fk_tag_s` FOREIGN KEY (`tag`) REFERENCES `tags` (`tag`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'評分'資料表
def _create_rates_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `rates` (
            `store_id` int NOT NULL,
            `avg_ratings` decimal(2,1) DEFAULT NULL,
            `total_ratings` int DEFAULT NULL,
            `sample_ratings` int DEFAULT NULL,
            `total_comments` int DEFAULT NULL,
            `real_rating` decimal(2,1) DEFAULT NULL,
            `environment_rating` decimal(4,1) DEFAULT NULL,
            `price_rating` decimal(4,1) DEFAULT NULL,
            `product_rating` decimal(4,1) DEFAULT NULL,
            `service_rating` decimal(4,1) DEFAULT NULL,
            `store_responses` int DEFAULT NULL,
            PRIMARY KEY (`store_id`),
            CONSTRAINT `fk_store_id_r` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'地點'資料表
def _create_locations_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `locations` (
            `store_id` int NOT NULL,
            `longitude` decimal(10,7) DEFAULT NULL,
            `latitude` decimal(10,7) DEFAULT NULL,
            `postal_code` varchar(6) DEFAULT NULL,
            `city` varchar(3) DEFAULT NULL,
            `dist` varchar(3) DEFAULT NULL,
            `vil` varchar(3) DEFAULT NULL,
            `details` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`store_id`),
            CONSTRAINT `fk_store_id_l` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'關鍵字'資料表
def _create_keywords_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `keywords` (
            `store_id` int NOT NULL,
            `word` varchar(20) NOT NULL,
            `count` int NOT NULL,
            `source` enum('google','comment') NOT NULL DEFAULT 'google',
            PRIMARY KEY (`word`,`store_id`),
            KEY `fk_store_id_k` (`store_id`),
            CONSTRAINT `fk_store_id_k` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'服務'資料表
def _create_services_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `services` (
            `store_id` int NOT NULL,
            `dine_in` tinyint DEFAULT NULL,
            `take_away` tinyint DEFAULT NULL,
            `delivery` tinyint DEFAULT NULL,
            PRIMARY KEY (`store_id`),
            CONSTRAINT `fk_store_id_s` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'評論'資料表
def _create_comments_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `comments` (
            `store_id` int NOT NULL,
            `sort` int NOT NULL,
            `contents` text NOT NULL,
            `time` varchar(20) NOT NULL,
            `rating` int NOT NULL,
            `contributor_id` varchar(255) NOT NULL,
            PRIMARY KEY (`sort`,`store_id`),
            KEY `store_id_idx` (`store_id`),
            KEY `comments_ibfk_2` (`contributor_id`),
            CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`contributor_id`) REFERENCES `contributors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `store_id` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'收藏夾'資料表
def _create_favorites_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `favorites` (
            `user_id` int NOT NULL,
            `store_id` int NOT NULL,
            `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`user_id`,`store_id`),
            KEY `fk_store_id` (`store_id`),
            CONSTRAINT `fk_store_id` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立'營業時間'資料表
def _create_openhours_table(c):
    c.execute('''
        CREATE TABLE IF NOT EXISTS `openhours` (
            `store_id` INT NOT NULL,
            `id` INT NOT NULL AUTO_INCREMENT,
            `day_of_week` VARCHAR(3) NOT NULL,
            `open_time` TIME NULL DEFAULT NULL,
            `close_time` TIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`, `store_id`),
            INDEX `fk_store_id_o_idx` (`store_id` ASC) VISIBLE,
            CONSTRAINT `fk_store_id_o` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# 建立資料庫
def create_database(c, database_name) -> bool:
    if not exists(c, database_name):
        c.execute(f"CREATE DATABASE IF NOT EXISTS {database_name}")
        c.execute(f"USE `{database_name}`")
        _create_administrators_table(c)
        _create_contributors_table(c)
        _create_tags_table(c)
        _create_members_table(c)
        _create_stores_table(c)
        _create_rates_table(c)
        _create_comments_table(c)
        _create_locations_table(c)
        _create_keywords_table(c)
        _create_services_table(c)
        _create_comments_table(c)
        _create_favorites_table(c)
        return True
    else:
        print(f"已存在名稱為'{database_name}'的資料庫。")
        return False

def drop_database(c, database_name) -> bool:
    if exists(c, database_name):
        c.execute(f'''
            DROP DATABASE `{database_name}`;
        ''')
        return True
    else:
        print(f"不存在名稱為'{database_name}'的資料庫。")
        return False

def truncate_database(c, database_name) -> bool:
    if exists(c, database_name):
        drop_database(c, database_name)
        create_database(c, database_name)
        return True
    else:
        print(f"不存在名稱為'{database_name}'的資料庫。")
        return False
