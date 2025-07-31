-- CMS Extensions for the Music Platform

-- Blog Posts
CREATE TABLE IF NOT EXISTS blog_posts (
                                          id SERIAL PRIMARY KEY,
                                          user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                          title TEXT NOT NULL,
                                          slug TEXT NOT NULL UNIQUE,
                                          content TEXT NOT NULL,
                                          excerpt TEXT,
                                          featured_image TEXT,
                                          status TEXT NOT NULL DEFAULT 'draft', -- draft, published, archived
                                          published_at TIMESTAMPTZ,
                                          created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                          updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Blog Categories
CREATE TABLE IF NOT EXISTS blog_categories (
                                               id SERIAL PRIMARY KEY,
                                               name TEXT NOT NULL UNIQUE,
                                               slug TEXT NOT NULL UNIQUE,
                                               description TEXT,
                                               created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                               updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Pivot blog_post ↔ blog_category
CREATE TABLE IF NOT EXISTS blog_post_categories (
                                                    post_id INTEGER NOT NULL REFERENCES blog_posts(id) ON DELETE CASCADE,
                                                    category_id INTEGER NOT NULL REFERENCES blog_categories(id) ON DELETE CASCADE,
                                                    PRIMARY KEY (post_id, category_id)
);

-- Blog Tags
CREATE TABLE IF NOT EXISTS blog_tags (
                                         id SERIAL PRIMARY KEY,
                                         name TEXT NOT NULL UNIQUE,
                                         slug TEXT NOT NULL UNIQUE,
                                         created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                         updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Pivot blog_post ↔ blog_tag
CREATE TABLE IF NOT EXISTS blog_post_tags (
                                              post_id INTEGER NOT NULL REFERENCES blog_posts(id) ON DELETE CASCADE,
                                              tag_id INTEGER NOT NULL REFERENCES blog_tags(id) ON DELETE CASCADE,
                                              PRIMARY KEY (post_id, tag_id)
);

-- Blog Comments
CREATE TABLE IF NOT EXISTS blog_comments (
                                             id SERIAL PRIMARY KEY,
                                             post_id INTEGER NOT NULL REFERENCES blog_posts(id) ON DELETE CASCADE,
                                             user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                                             parent_id INTEGER REFERENCES blog_comments(id) ON DELETE CASCADE,
                                             author_name TEXT,
                                             author_email TEXT,
                                             content TEXT NOT NULL,
                                             status TEXT NOT NULL DEFAULT 'pending', -- pending, approved, spam
                                             created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                             updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Employees
CREATE TABLE IF NOT EXISTS employees (
                                         id SERIAL PRIMARY KEY,
                                         user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                                         name TEXT NOT NULL,
                                         position TEXT NOT NULL,
                                         department TEXT NOT NULL,
                                         bio TEXT,
                                         photo TEXT,
                                         email TEXT,
                                         phone TEXT,
                                         hire_date DATE,
                                         status TEXT NOT NULL DEFAULT 'active', -- active, inactive
                                         created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                         updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Store Products
CREATE TABLE IF NOT EXISTS store_products (
                                              id SERIAL PRIMARY KEY,
                                              name TEXT NOT NULL,
                                              slug TEXT NOT NULL UNIQUE,
                                              description TEXT,
                                              price NUMERIC NOT NULL,
                                              sale_price NUMERIC,
                                              stock INTEGER NOT NULL DEFAULT 0,
                                              sku TEXT UNIQUE,
                                              featured BOOLEAN NOT NULL DEFAULT FALSE,
                                              status TEXT NOT NULL DEFAULT 'draft', -- draft, published, archived
                                              created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                              updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Store Product Categories
CREATE TABLE IF NOT EXISTS store_categories (
                                                id SERIAL PRIMARY KEY,
                                                name TEXT NOT NULL UNIQUE,
                                                slug TEXT NOT NULL UNIQUE,
                                                description TEXT,
                                                created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                                updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Pivot store_product ↔ store_category
CREATE TABLE IF NOT EXISTS store_product_categories (
                                                        product_id INTEGER NOT NULL REFERENCES store_products(id) ON DELETE CASCADE,
                                                        category_id INTEGER NOT NULL REFERENCES store_categories(id) ON DELETE CASCADE,
                                                        PRIMARY KEY (product_id, category_id)
);

-- Store Product Images
CREATE TABLE IF NOT EXISTS store_product_images (
                                                    id SERIAL PRIMARY KEY,
                                                    product_id INTEGER NOT NULL REFERENCES store_products(id) ON DELETE CASCADE,
                                                    image_path TEXT NOT NULL,
                                                    sort_order INTEGER NOT NULL DEFAULT 0,
                                                    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                                    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Playlists
CREATE TABLE IF NOT EXISTS playlists (
                                         id SERIAL PRIMARY KEY,
                                         user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                         name TEXT NOT NULL,
                                         description TEXT,
                                         cover_image TEXT,
                                         is_public BOOLEAN NOT NULL DEFAULT FALSE,
                                         created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                         updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Pivot playlist ↔ track
CREATE TABLE IF NOT EXISTS playlist_tracks (
                                               playlist_id INTEGER NOT NULL REFERENCES playlists(id) ON DELETE CASCADE,
                                               track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                               sort_order INTEGER NOT NULL DEFAULT 0,
                                               created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                               PRIMARY KEY (playlist_id, track_id)
);

-- Shopping Cart
CREATE TABLE IF NOT EXISTS carts (
                                     id SERIAL PRIMARY KEY,
                                     user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                                     session_id TEXT,
                                     created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                     updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                     UNIQUE(user_id, session_id)
);

-- Cart Items for Tracks
CREATE TABLE IF NOT EXISTS cart_tracks (
                                           id SERIAL PRIMARY KEY,
                                           cart_id INTEGER NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
                                           track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                           license_type TEXT NOT NULL DEFAULT 'standard', -- standard, extended, exclusive
                                           price NUMERIC NOT NULL,
                                           created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                           updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                           UNIQUE(cart_id, track_id)
);

-- Cart Items for Store Products
CREATE TABLE IF NOT EXISTS cart_products (
                                             id SERIAL PRIMARY KEY,
                                             cart_id INTEGER NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
                                             product_id INTEGER NOT NULL REFERENCES store_products(id) ON DELETE CASCADE,
                                             quantity INTEGER NOT NULL DEFAULT 1,
                                             price NUMERIC NOT NULL,
                                             created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                             updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                             UNIQUE(cart_id, product_id)
);

-- Orders
CREATE TABLE IF NOT EXISTS orders (
                                      id SERIAL PRIMARY KEY,
                                      user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                                      order_number TEXT NOT NULL UNIQUE,
                                      status TEXT NOT NULL DEFAULT 'pending', -- pending, processing, completed, cancelled
                                      total NUMERIC NOT NULL,
                                      subtotal NUMERIC NOT NULL,
                                      tax NUMERIC NOT NULL DEFAULT 0,
                                      discount NUMERIC NOT NULL DEFAULT 0,
                                      shipping NUMERIC NOT NULL DEFAULT 0,
                                      billing_name TEXT NOT NULL,
                                      billing_email TEXT NOT NULL,
                                      billing_phone TEXT,
                                      billing_address TEXT,
                                      billing_city TEXT,
                                      billing_state TEXT,
                                      billing_zip TEXT,
                                      billing_country TEXT,
                                      shipping_name TEXT,
                                      shipping_address TEXT,
                                      shipping_city TEXT,
                                      shipping_state TEXT,
                                      shipping_zip TEXT,
                                      shipping_country TEXT,
                                      payment_method TEXT,
                                      payment_id TEXT,
                                      notes TEXT,
                                      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                      updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Order Items for Tracks
CREATE TABLE IF NOT EXISTS order_tracks (
                                            id SERIAL PRIMARY KEY,
                                            order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
                                            track_id INTEGER REFERENCES tracks(id) ON DELETE SET NULL,
                                            track_name TEXT NOT NULL,
                                            license_type TEXT NOT NULL,
                                            price NUMERIC NOT NULL,
                                            created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                            updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Order Items for Store Products
CREATE TABLE IF NOT EXISTS order_products (
                                              id SERIAL PRIMARY KEY,
                                              order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
                                              product_id INTEGER REFERENCES store_products(id) ON DELETE SET NULL,
                                              product_name TEXT NOT NULL,
                                              quantity INTEGER NOT NULL,
                                              price NUMERIC NOT NULL,
                                              created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                              updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Track Licenses
CREATE TABLE IF NOT EXISTS track_licenses (
                                              id SERIAL PRIMARY KEY,
                                              name TEXT NOT NULL UNIQUE,
                                              description TEXT,
                                              price_multiplier NUMERIC NOT NULL DEFAULT 1.0,
                                              rights TEXT NOT NULL,
                                              created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                              updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Insert default license types
INSERT INTO track_licenses (name, description, price_multiplier, rights)
VALUES
    ('Standard', 'Basic license for personal use', 1.0, 'Personal use only, no commercial rights'),
    ('Commercial', 'License for commercial projects', 2.0, 'Commercial use in one project, no resale'),
    ('Extended', 'Extended license for multiple commercial projects', 5.0, 'Multiple commercial projects, no resale'),
    ('Exclusive', 'Exclusive rights to the track', 20.0, 'Full ownership and exclusive rights')
ON CONFLICT (name) DO NOTHING;

-- Track Prices
CREATE TABLE IF NOT EXISTS track_prices (
                                            id SERIAL PRIMARY KEY,
                                            track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                            license_id INTEGER NOT NULL REFERENCES track_licenses(id) ON DELETE CASCADE,
                                            price NUMERIC NOT NULL,
                                            created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                            updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                            UNIQUE(track_id, license_id)
);

-- User Purchased Tracks
CREATE TABLE IF NOT EXISTS user_purchased_tracks (
                                                     id SERIAL PRIMARY KEY,
                                                     user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                                     track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                                     order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
                                                     license_type TEXT NOT NULL,
                                                     download_count INTEGER NOT NULL DEFAULT 0,
                                                     created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                                     updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                                     UNIQUE(user_id, track_id, order_id)
);

-- User Favorites
CREATE TABLE IF NOT EXISTS user_favorites (
                                              id SERIAL PRIMARY KEY,
                                              user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                                              track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                              created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                              UNIQUE(user_id, track_id)
);

-- User Play History
CREATE TABLE IF NOT EXISTS user_play_history (
                                                 id SERIAL PRIMARY KEY,
                                                 user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                                                 track_id INTEGER NOT NULL REFERENCES tracks(id) ON DELETE CASCADE,
                                                 played_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                                                 duration INTEGER NOT NULL DEFAULT 0
);

-- Extend users table with additional fields
ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS website TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_twitter TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_facebook TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_instagram TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_youtube TEXT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS newsletter_subscribed BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMPTZ;

-- Add duration field to tracks table if not exists
ALTER TABLE tracks ADD COLUMN IF NOT EXISTS duration INTEGER NOT NULL DEFAULT 0;

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_blog_posts_user_id ON blog_posts(user_id);
CREATE INDEX IF NOT EXISTS idx_blog_posts_status ON blog_posts(status);
CREATE INDEX IF NOT EXISTS idx_blog_posts_published_at ON blog_posts(published_at);
CREATE INDEX IF NOT EXISTS idx_blog_comments_post_id ON blog_comments(post_id);
CREATE INDEX IF NOT EXISTS idx_blog_comments_user_id ON blog_comments(user_id);
CREATE INDEX IF NOT EXISTS idx_employees_user_id ON employees(user_id);
CREATE INDEX IF NOT EXISTS idx_store_products_status ON store_products(status);
CREATE INDEX IF NOT EXISTS idx_playlists_user_id ON playlists(user_id);
CREATE INDEX IF NOT EXISTS idx_playlist_tracks_playlist_id ON playlist_tracks(playlist_id);
CREATE INDEX IF NOT EXISTS idx_playlist_tracks_track_id ON playlist_tracks(track_id);
CREATE INDEX IF NOT EXISTS idx_carts_user_id ON carts(user_id);
CREATE INDEX IF NOT EXISTS idx_carts_session_id ON carts(session_id);
CREATE INDEX IF NOT EXISTS idx_cart_tracks_cart_id ON cart_tracks(cart_id);
CREATE INDEX IF NOT EXISTS idx_cart_products_cart_id ON cart_products(cart_id);
CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_order_tracks_order_id ON order_tracks(order_id);
CREATE INDEX IF NOT EXISTS idx_order_products_order_id ON order_products(order_id);
CREATE INDEX IF NOT EXISTS idx_track_prices_track_id ON track_prices(track_id);
CREATE INDEX IF NOT EXISTS idx_user_purchased_tracks_user_id ON user_purchased_tracks(user_id);
CREATE INDEX IF NOT EXISTS idx_user_purchased_tracks_track_id ON user_purchased_tracks(track_id);
CREATE INDEX IF NOT EXISTS idx_user_favorites_user_id ON user_favorites(user_id);
CREATE INDEX IF NOT EXISTS idx_user_favorites_track_id ON user_favorites(track_id);
CREATE INDEX IF NOT EXISTS idx_user_play_history_user_id ON user_play_history(user_id);
CREATE INDEX IF NOT EXISTS idx_user_play_history_track_id ON user_play_history(track_id);
CREATE INDEX IF NOT EXISTS idx_user_play_history_played_at ON user_play_history(played_at);
