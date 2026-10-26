-- Mars Pillow database tables (MySQL / MariaDB)
-- Hostinger: hPanel > Databases > phpMyAdmin > select your database > Import > choose this file > Go

CREATE TABLE IF NOT EXISTS orders (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_number       VARCHAR(20)  NOT NULL UNIQUE,
  status             VARCHAR(20)  NOT NULL DEFAULT 'pending',  -- pending, paid, shipped, cancelled, refunded
  payment_method     VARCHAR(20)  NOT NULL,                    -- stripe, paypal
  payment_ref        VARCHAR(120) NULL,                        -- Stripe Checkout Session id / PayPal order id
  payment_capture_id VARCHAR(120) NULL,                        -- Stripe PaymentIntent id / PayPal capture id
  currency           CHAR(3)      NOT NULL DEFAULT 'USD',
  subtotal_cents     INT UNSIGNED NOT NULL,
  shipping_cents     INT UNSIGNED NOT NULL DEFAULT 0,
  discount_cents     INT UNSIGNED NOT NULL DEFAULT 0,
  coupon_code        VARCHAR(40)  NULL,
  total_cents        INT UNSIGNED NOT NULL,
  customer_name      VARCHAR(190) NULL,
  customer_email     VARCHAR(190) NULL,
  ship_line1         VARCHAR(190) NULL,
  ship_line2         VARCHAR(190) NULL,
  ship_city          VARCHAR(120) NULL,
  ship_state         VARCHAR(60)  NULL,
  ship_postal        VARCHAR(30)  NULL,
  ship_country       CHAR(2)      NULL,
  tracking_number    VARCHAR(120) NULL,
  emails_sent        TINYINT(1)   NOT NULL DEFAULT 0,
  review_token       VARCHAR(64)  NULL,
  review_requested_at DATETIME    NULL,
  marketing_consent  TINYINT(1)   NOT NULL DEFAULT 0,      -- visitor accepted cookies (allows Meta server events)
  created_at         DATETIME     NOT NULL,
  paid_at            DATETIME     NULL,
  shipped_at         DATETIME     NULL,
  INDEX idx_payment_ref (payment_ref),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id         INT UNSIGNED NOT NULL,
  offer_id         VARCHAR(40)  NOT NULL,
  name             VARCHAR(120) NOT NULL,
  unit_price_cents INT UNSIGNED NOT NULL,
  qty              INT UNSIGNED NOT NULL,
  line_total_cents INT UNSIGNED NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  subject    VARCHAR(190) NOT NULL,
  message    TEXT         NOT NULL,
  ip         VARCHAR(45)  NULL,
  created_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verified reviews: only customers with a paid order can leave one (link in the review-request email)
CREATE TABLE IF NOT EXISTS reviews (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id    INT UNSIGNED NOT NULL,
  name        VARCHAR(80)  NOT NULL,
  rating      TINYINT      NOT NULL,
  title       VARCHAR(120) NOT NULL,
  body        TEXT         NOT NULL,
  sleep_position VARCHAR(20) NULL,
  status      VARCHAR(20)  NOT NULL DEFAULT 'pending',  -- pending, approved, hidden
  created_at  DATETIME     NOT NULL,
  UNIQUE KEY uq_review_order (order_id),
  CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Emails collected from the popup and the quiz, with the visitor's cart for reminder emails
CREATE TABLE IF NOT EXISTS subscribers (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(190) NOT NULL UNIQUE,
  source        VARCHAR(30)  NOT NULL,               -- popup, quiz
  quiz_result   VARCHAR(60)  NULL,
  cart_json     TEXT         NULL,
  cart_updated_at DATETIME   NULL,
  reminder_sent_at DATETIME  NULL,
  restore_token VARCHAR(64)  NOT NULL,
  unsubscribed  TINYINT(1)   NOT NULL DEFAULT 0,
  converted     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at    DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
