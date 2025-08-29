CREATE DATABASE IF NOT EXISTS vkitchen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vkitchen;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  uid INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS recipes;
CREATE TABLE recipes (
  rid INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description VARCHAR(500) NOT NULL,
  type ENUM('French','Italian','Chinese','Indian','Mexican','others','Asian') NOT NULL DEFAULT 'others',
  cookingtime INT NOT NULL,
  ingredients TEXT NOT NULL,
  instructions TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  uid INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (username, password, email)
VALUES
('demo_user', '$2y$10$7e0Qb6O6b3zsE6cN/7fyhOVD1dQ/1/2x1sQqzBYPevcWAdH8kP1q6', 'demo@example.com');


INSERT INTO recipes (name, description, type, cookingtime, ingredients, instructions, image, uid)
VALUES
('Japanese Miso Ramen',
 'Comforting bowl of homemade miso ramen with soft-boiled eggs and mushrooms.',
 'Asian', 40,
 'Ramen noodles; 4 cups dashi or chicken stock; 3 tbsp white miso paste; 200g shiitake; 2 eggs; spring onions; soy sauce; sesame oil; garlic; ginger',
 '1) Make stock and sauté garlic+ginger+shiitake. 2) Add stock and miso; simmer. 3) Cook noodles separately. 4) Soft-boil eggs (7 mins). 5) Assemble bowl: noodles, stock, toppings, egg.',
 'miso_ramen.jpg', 1),
('Thai Green Curry',
 'Fragrant Thai green curry with coconut milk, vegetables and basil.',
 'Asian', 30,
 'Green curry paste; 400ml coconut milk; eggplant; green beans; tofu or chicken; fish sauce; palm sugar; Thai basil; jasmine rice',
 '1) Fry green curry paste briefly. 2) Add coconut milk and bring to simmer. 3) Add vegetables and protein; cook until tender. 4) Season with fish sauce and palm sugar. 5) Stir in basil and serve with rice.',
 'thai_green_curry.jpg', 1),
('Korean Bibimbap',
 'Mixed rice bowl with sautéed vegetables, gochujang, and a fried egg.',
 'Asian', 25,
 'Cooked short-grain rice; spinach; carrots; bean sprouts; shiitake; soy sauce; sesame oil; gochujang; egg; sesame seeds',
 '1) Prepare vegetables: sauté each separately with salt and sesame oil. 2) Place rice in bowl, arrange veggies on top. 3) Top with fried egg and a spoon of gochujang. 4) Mix everything and enjoy.',
 'bibimbap.jpg', 1);
