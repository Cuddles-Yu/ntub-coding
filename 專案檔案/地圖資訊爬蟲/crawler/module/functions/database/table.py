
# '管理者'資料表
def create_administrators(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `administrators` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email_UNIQUE` (`email`)
        )
    ''')

# '會員'資料表
def create_members(cursor):
    cursor.execute('''
        CREATE TABLE members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
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

# '商家'資料表
def create_stores(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `stores` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(255) NOT NULL,
            `branch_title` varchar(255) DEFAULT NULL,
            `branch_name` varchar(255) DEFAULT NULL,
            `tag` varchar(20) DEFAULT NULL,
            `mark` ENUM('客家', '環保') DEFAULT NULL,
            `preview_image` varchar(255) DEFAULT NULL,
            `link` varchar(2000) NOT NULL,
            `website` varchar(2000) DEFAULT NULL,
            `phone_number` varchar(30) DEFAULT NULL,
            `last_update` varchar(20) DEFAULT NULL,
            `crawler_state` varchar(10) DEFAULT '建立',
            `crawler_description` varchar(100) DEFAULT NULL,
            `crawler_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `name_UNIQUE` (`name`),
            KEY `fk_tag_s_idx` (`tag`),
            FOREIGN KEY (`tag`) REFERENCES `tags` (`tag`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '評論'資料表
def create_comments(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `comments` (
            `store_id` int NOT NULL,
            `id` int NOT NULL,
            `data_id` varchar(50) NOT NULL,
            `contents` text,
            `time` varchar(20) NOT NULL,
            `rating` int NOT NULL,
            `has_image` tinyint NOT NULL,
            `food_rating` int DEFAULT NULL,
            `service_rating` int DEFAULT NULL,
            `atmosphere_rating` int DEFAULT NULL,
            `contributor_level` int NOT NULL,
            `environment_state` varchar(10) DEFAULT NULL,
            `price_state` varchar(10) DEFAULT NULL,
            `product_state` varchar(10) DEFAULT NULL,
            `service_state` varchar(10) DEFAULT NULL,
            `sample_of_most_relevant` tinyint NOT NULL DEFAULT '0',
            `sample_of_highest_rating` tinyint NOT NULL DEFAULT '0',
            `sample_of_lowest_rating` tinyint NOT NULL DEFAULT '0',
            PRIMARY KEY (`store_id`,`id`),
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '地點'資料表
def create_locations(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `locations` (
            `store_id` int NOT NULL PRIMARY KEY,
            `longitude` decimal(10,7) DEFAULT NULL,
            `latitude` decimal(10,7) DEFAULT NULL,
            `postal_code` varchar(6) DEFAULT NULL,
            `city` varchar(3) DEFAULT NULL,
            `dist` varchar(3) DEFAULT NULL,
            `vil` varchar(3) DEFAULT NULL,
            `details` varchar(255) DEFAULT NULL,
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '評分'資料表
def create_rates(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `rates` (
            `store_id` int NOT NULL PRIMARY KEY,
            `avg_ratings` decimal(2,1) DEFAULT NULL,
            `total_reviews` int DEFAULT NULL,
            `total_browses` int DEFAULT NULL,
            `total_samples` int DEFAULT NULL,
            `total_withcomments` int DEFAULT NULL,
            `total_withoutcomments` int DEFAULT NULL,
            `mixreviews_count` int DEFAULT NULL,
            `additionalcomments_count` int DEFAULT NULL,
            `real_rating` decimal(2,1) DEFAULT NULL,
            `comments_analysis` TINYINT NULL DEFAULT 0,
            `environment_rating` decimal(6,3) DEFAULT NULL,
            `price_rating` decimal(6,3) DEFAULT NULL,
            `product_rating` decimal(6,3) DEFAULT NULL,
            `service_rating` decimal(6,3) DEFAULT NULL,
            `store_responses` int DEFAULT NULL,
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '服務'資料表
def create_services(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `services` (
            `store_id` int NOT NULL,
            `id` int NOT NULL,
            `property` varchar(20) NOT NULL,
            `category` varchar(20) NOT NULL,
            `state` tinyint DEFAULT NULL,
            PRIMARY KEY (`store_id`,`id`),
            KEY `fk_store_id_s_idx` (`store_id`),
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '營業時間'資料表
def create_openhours(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `openhours` (
            `store_id` int NOT NULL,
            `id` int NOT NULL,
            `day_of_week` varchar(3) NOT NULL,
            `open_time` time DEFAULT NULL,
            `close_time` time DEFAULT NULL,
            PRIMARY KEY (`store_id`,`id`),
            KEY `fk_store_id_o_idx` (`store_id`),
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '收藏夾'資料表
def create_favorites(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `favorites` (
            `user_id` int NOT NULL,
            `store_id` int NOT NULL,
            `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`user_id`,`store_id`),
            KEY `fk_store_id` (`store_id`),
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`user_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '收藏'資料表
def create_histories(cursor):
    cursor.execute('''
        CREATE TABLE histories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT,
            action VARCHAR(30),
            target VARCHAR(255),
            operation_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '偏好'資料表
def create_preferences(cursor):
    cursor.execute('''
        CREATE TABLE preferences (
            member_id INT PRIMARY KEY,
            atmosphere_weight INT DEFAULT 50,
            price_weight INT DEFAULT 50,
            product_weight INT DEFAULT 50,
            service_weight INT DEFAULT 50,
            search_radius INT DEFAULT 1500,                 
            open_now BOOLEAN DEFAULT FALSE,
            parking BOOLEAN DEFAULT FALSE,
            wheelchair_accessible BOOLEAN DEFAULT FALSE,
            vegetarian BOOLEAN DEFAULT FALSE,
            healthy BOOLEAN DEFAULT FALSE,
            kids_friendly BOOLEAN DEFAULT FALSE,
            pets_friendly BOOLEAN DEFAULT FALSE,
            gender_friendly BOOLEAN DEFAULT FALSE,
            delivery BOOLEAN DEFAULT FALSE,
            takeaway BOOLEAN DEFAULT FALSE,
            dine_in BOOLEAN DEFAULT FALSE,
            breakfast BOOLEAN DEFAULT FALSE,
            brunch BOOLEAN DEFAULT FALSE,
            lunch BOOLEAN DEFAULT FALSE,
            dinner BOOLEAN DEFAULT FALSE,       
            reservation BOOLEAN DEFAULT FALSE,
            group_friendly BOOLEAN DEFAULT FALSE,
            family_friendly BOOLEAN DEFAULT FALSE,            
            toilet BOOLEAN DEFAULT FALSE,
            wifi BOOLEAN DEFAULT FALSE,
            cash BOOLEAN DEFAULT FALSE,
            credit_card BOOLEAN DEFAULT FALSE,
            debit_card BOOLEAN DEFAULT FALSE,
            mobile_payment BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '金鑰'資料表
def create_tokens(cursor):
    cursor.execute('''
        CREATE TABLE tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT,
            token VARCHAR(255) NOT NULL,
            create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expiration_time TIMESTAMP NOT NULL,
            FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

# '關鍵字'資料表
def create_keywords(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `keywords` (
            `store_id` int NOT NULL,
            `word` varchar(50) NOT NULL,
            `source` enum('google','comment','recommend','recommend-ignore') NOT NULL DEFAULT 'google',
            `count` int NOT NULL,
            `image_url` varchar(2000) DEFAULT NULL,
            `source_url` varchar(2000) DEFAULT NULL,
            PRIMARY KEY (`store_id`,`word`,`source`),
            KEY `fk_store_id_k` (`store_id`,`word`,`source`),
            FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')

def create_marks(cursor):
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS `marks` (
            `store_id` int NOT NULL,
            `comment_id` int NOT NULL,
            `id` int NOT NULL,
            `contents` varchar(255) NOT NULL,
            `target` enum('環境','產品','服務','售價') NOT NULL,
            `state` enum('正面','負面','喜好') NOT NULL,
            PRIMARY KEY (`store_id`,`comment_id`,`id`),
            KEY `fk_comment_id_m_idx` (`comment_id`),
            FOREIGN KEY (`store_id`, `comment_id`) REFERENCES `comments` (`store_id`, `id`) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ''')
