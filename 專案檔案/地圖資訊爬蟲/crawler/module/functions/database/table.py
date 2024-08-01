
# '管理者'資料表
def create_administrators(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `administrators` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email_UNIQUE` (`email`)
        )
    ''')

# '標籤'資料表
def create_tags(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `tags` (
            `tag` varchar(20) NOT NULL,
            `category` varchar(20) DEFAULT NULL,
            PRIMARY KEY (`tag`)
        )
    ''')

# '會員'資料表
def create_members(cursor):
    cursor.execute('''
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

# '商家'資料表
def create_stores(cursor):
    cursor.execute('''
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
            `crawler_state` varchar(10) DEFAULT '',
            `crawler_description` varchar(100) DEFAULT NULL,
            `crawler_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name_UNIQUE` (`name`),
            KEY `fk_tag_s_idx` (`tag`),
            CONSTRAINT `fk_tag_s` FOREIGN KEY (`tag`) REFERENCES `tags` (`tag`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '評分'資料表
def create_rates(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `rates` (
            `store_id` int NOT NULL,
            `avg_ratings` decimal(2,1) DEFAULT NULL,
            `total_reviews` int DEFAULT NULL,
            `total_browses` int DEFAULT NULL,
            `total_samples` int DEFAULT NULL,
            `total_withcomments` int DEFAULT NULL,
            `total_withoutcomments` int DEFAULT NULL,
            `mixreviews_count` int DEFAULT NULL,
            `additionalcomments_count` int DEFAULT NULL,
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

# '地點'資料表
def create_locations(cursor):
    cursor.execute('''
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

# '關鍵字'資料表
def create_keywords(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `keywords` (
            `store_id` int NOT NULL,
            `word` varchar(50) NOT NULL,
            `count` int NOT NULL,
            `source` enum('google','comment','recommend') NOT NULL DEFAULT 'google',
            `image_url` varchar(2000) DEFAULT NULL,
            `source_url` varchar(2000) DEFAULT NULL,
            PRIMARY KEY (`store_id`, `word`),
            KEY `fk_store_id_k` (`store_id`),
            CONSTRAINT `fk_store_id_k` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '服務'資料表
def create_services(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `services` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `store_id` INT NOT NULL,
            `property` VARCHAR(20) NOT NULL,
            `category` VARCHAR(20) NOT NULL,
            `state` TINYINT NULL DEFAULT NULL,
            PRIMARY KEY (`id`, `store_id`),
            INDEX `fk_store_id_s_idx` (`store_id` ASC) VISIBLE,
            CONSTRAINT `fk_store_id_s` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '評論'資料表
def create_comments(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `comments` (
            `store_id` int NOT NULL,
            `index` int NOT NULL, 
            `data_id` varchar(50) NOT NULL,
            `contents` text DEFAULT NULL,
            `time` varchar(20) NOT NULL,
            `rating` int NOT NULL,       
            `has_image` TINYINT NOT NULL,    
            `food_rating` int DEFAULT NULL,
            `service_rating` int DEFAULT NULL,
            `atmosphere_rating` int DEFAULT NULL,
            `contributor_level` INT NOT NULL,
            `environment_state` VARCHAR(10) DEFAULT NULL,
            `price_state` VARCHAR(10) DEFAULT NULL,
            `product_state` VARCHAR(10) DEFAULT NULL,
            `service_state` VARCHAR(10) DEFAULT NULL,
            `sample_of_most_relevant` TINYINT NOT NULL DEFAULT '0',    
            `sample_of_highest_rating` TINYINT NOT NULL DEFAULT '0',          
            `sample_of_lowest_rating` TINYINT NOT NULL DEFAULT '0', 
            PRIMARY KEY (`store_id`, `index`),
            KEY `store_id_idx` (`store_id`),
            CONSTRAINT `store_id` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '收藏夾'資料表
def create_favorites(cursor):
    cursor.execute('''
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

# '營業時間'資料表
def create_openhours(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `openhours` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `store_id` INT NOT NULL,
            `day_of_week` VARCHAR(3) NOT NULL,
            `open_time` TIME NULL DEFAULT NULL,
            `close_time` TIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`, `store_id`),
            INDEX `fk_store_id_o_idx` (`store_id` ASC) VISIBLE,
            CONSTRAINT `fk_store_id_o` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')
