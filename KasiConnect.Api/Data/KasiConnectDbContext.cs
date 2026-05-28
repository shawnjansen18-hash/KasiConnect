using KasiConnect.Api.Models;
using Microsoft.EntityFrameworkCore;

namespace KasiConnect.Api.Data
{
    public class KasiConnectDbContext :DbContext
    {
        public KasiConnectDbContext(DbContextOptions<KasiConnectDbContext> options) : base(options) 
        {
            
        }

        public DbSet<Product> Products => Set<Product>();
        public DbSet<Review> Reviews => Set<Review>();
        public DbSet<User> Users => Set<User>();
        public DbSet<Order> Orders => Set<Order>();

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            modelBuilder.Entity<Product>(entity =>
            {
                entity.ToTable("products");

                entity.HasKey(product => product.Id);               

                entity.Property(product => product.Id).HasColumnName("id");
                entity.Property(product => product.UserId).HasColumnName("user_id");
                entity.Property(product => product.Title).HasColumnName("title");
                entity.Property(product => product.Description).HasColumnName("description");
                entity.Property(product => product.Price).HasColumnName("price");
                entity.Property(product => product.CreatedAt).HasColumnName("created_at");
                entity.Property(product => product.Image).HasColumnName("image");
            });

            modelBuilder.Entity<Review>(entity =>
            {
                entity.ToTable("reviews");

                entity.HasKey(review => review.Id);

                entity.Property(review => review.Id).HasColumnName("id");
                entity.Property(review => review.ProductId).HasColumnName("product_id");
                entity.Property(review => review.UserId).HasColumnName("user_id");
                entity.Property(review => review.Rating).HasColumnName("rating");
                entity.Property(review => review.ReviewText).HasColumnName("review");
                entity.Property(review => review.CreatedAt).HasColumnName("created_at");
            });

            modelBuilder.Entity<User>(entity =>
            {
                entity.ToTable("users");

                entity.HasKey(user => user.Id);

                entity.Property(user => user.Id).HasColumnName("id");
                entity.Property(user => user.Name).HasColumnName("name");
                entity.Property(user => user.Email).HasColumnName("email");
                entity.Property(user => user.Password).HasColumnName("password");
            });

            modelBuilder.Entity<Order>(entity =>
            {
                entity.ToTable("orders");

                entity.HasKey(order => order.Id);

                entity.Property(order => order.Id).HasColumnName("id");
                entity.Property(order => order.ProductId).HasColumnName("product_id");
                entity.Property(order => order.BuyerId).HasColumnName("buyer_id");
                entity.Property(order => order.SellerId).HasColumnName("seller_id");
                entity.Property(order => order.Status).HasColumnName("status");
                entity.Property(order => order.CreatedAt).HasColumnName("created_at");
            });

        }

    }
}
