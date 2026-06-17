using KasiConnect.Api.Data;
using KasiConnect.Api.DTO;
using KasiConnect.Api.Models;
using Microsoft.AspNetCore.Http.HttpResults;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.SignalR;
using Microsoft.EntityFrameworkCore;
using Microsoft.AspNetCore.Authorization;
using System.Security.Claims;

namespace KasiConnect.Api.Controllers
{
    [ApiController]
    [Route("api/[controller]")] //ASP.NET will use controller name for the URL
    public class ProductsController :ControllerBase
    {
        private readonly KasiConnectDbContext _context;
        private readonly IWebHostEnvironment _environment;

        public ProductsController(KasiConnectDbContext context, IWebHostEnvironment environment)
        {
            _context = context;
            _environment = environment;
        }

        
        [HttpGet]
        public async Task<IActionResult> GetProducts([FromQuery] string? search)
        {
             var query = _context.Products.AsQueryable();
            
            if(!string.IsNullOrWhiteSpace(search))
            {
                query = query.Where(product => product.Title != null && product.Title.Contains(search));
            }

            var products = await query
        .OrderByDescending(product => product.CreatedAt)
        .Select(product => new ProductDto
        {
            Id = product.Id,
            Title = product.Title,
            Description = product.Description,
            Price = product.Price,
            ImageUrl = product.Image == null
                ? null
                : $"/KasiConnect/Images/{product.Image}",
            CreatedAt = product.CreatedAt
        })
        .ToListAsync();

            return Ok(products);
        }
        
        /*----------------------------
                Get Product
        ----------------------------*/
        [HttpGet("{id:int}")]
        public async Task<IActionResult> GetProduct(int id)
        {
            var product = await _context.Products.Where(product => product.Id == id)
                .Select(product => new ProductDto
                {
                    Id = product.Id,
                    UserId = product.UserId,
                    Title = product.Title,
                    Description = product.Description,
                    Price = product.Price,
                    ImageUrl = product.Image == null
                    ? null
                    : $"/KasiConnect/Images/{product.Image}",
                    CreatedAt = product.CreatedAt
                })
            .FirstOrDefaultAsync();


            if(product == null)
            {
                return NotFound();
            }

            return Ok(product);
        }

        /*----------------------------
                Get Reviews
        ----------------------------*/

        [HttpGet("{id:int}/reviews")]
        public async Task<IActionResult> GetProductReviews(int id)
        {
            var productExists = await _context.Products.AnyAsync(product => product.Id == id);

            if(!productExists)
            {
                return NotFound("Product not found");
            }

            var reviews = await (from review in _context.Reviews join user in _context.Users 
                          on review.UserId equals user.Id where review.ProductId == id 
                          orderby review.CreatedAt descending select new ReviewDto
                {
                    Id = review.Id,
                    ProductId = review.ProductId,
                    UserId = review.UserId,
                    UserName = user.Name,
                    Rating = review.Rating,
                    ReviewText = review.ReviewText,
                    CreatedAt = review.CreatedAt
                })
                .ToListAsync();
                
                return Ok(reviews);
        }

        /*----------------------------
             Createing the reviews
        ----------------------------*/
        [Authorize]
        [HttpPost("{id:int}/reviews")]
        public async Task<IActionResult> CreateProductReview(int id, CreateReviewDto createReviewDto)
        {
            var userIdValue = User.FindFirstValue(ClaimTypes.NameIdentifier);
            if(!int.TryParse(userIdValue, out var userId))
            {
                return Unauthorized("Invalid Token.");
            }
            
            var productExists = await _context.Products.AnyAsync(productExists => productExists.Id == id);

            if(!productExists)
            {
                return NotFound("Product not found.");
            }

            var userExists = await _context.Users.AnyAsync(user => user.Id == userId);

            if (!userExists)
            {
                return BadRequest("User does not exist!");
            }

            var review = new Review
            {
                ProductId = id,
                UserId = userId,
                Rating = createReviewDto.Rating,
                ReviewText = createReviewDto.ReviewText
            };

            _context.Reviews.Add(review);
            await _context.SaveChangesAsync();

            var userName = await _context.Users.Where(user => user.Id == review.UserId)
                           .Select(user => user.Name).FirstOrDefaultAsync();

            var reviewDto = new ReviewDto
            {
                Id = review.Id,
                ProductId = review.ProductId,
                Rating = review.Rating,
                ReviewText = review.ReviewText,
                CreatedAt = review.CreatedAt
            };

            return CreatedAtAction(nameof(GetProductReviews), new { id }, reviewDto);

        }

        /*----------------------------
             Create Products
        ----------------------------*/
        [Authorize]
        [HttpPost]
        public async Task<IActionResult> CreateProduct([FromForm]CreateProductDto createProductDto)
        {
            var userIdValue = User.FindFirstValue(ClaimTypes.NameIdentifier);

            if(!int.TryParse(userIdValue, out var userId))
            {
                return Unauthorized("Invalid token.");
            }

            var sellerExists = await _context.Users.AnyAsync(user => user.Id == userId);

            if(!sellerExists)
            {
                return BadRequest("Seller does not exists");
            }

            var allowedExtensions = new[] { ".jpg", ".jpeg", ".png", ".webp" };
            var extension = Path.GetExtension(createProductDto.ImageFile.FileName).ToLowerInvariant();

            if (!allowedExtensions.Contains(extension))
            {
                return BadRequest("Only JPG, PNG, and WEBP images are allowed");
            }

            var imgName = $"{Guid.NewGuid()}{extension}";
            var imgFolder = Path.GetFullPath(Path.Combine(_environment.ContentRootPath, "..", "Images"));

            Directory.CreateDirectory(imgFolder);

            var imgPath = Path.Combine(imgFolder, imgName);

            using (var stream = new FileStream(imgPath, FileMode.Create))
            {
                await createProductDto.ImageFile.CopyToAsync(stream);
            }



            var product = new Product
            {
                UserId = userId,
                Title = createProductDto.Title,
                Description = createProductDto.Description,
                Price = createProductDto.Price,
                Image = imgName
            };

            _context.Products.Add(product);
            await _context.SaveChangesAsync();

            var productDto = new ProductDto
            {
                Id = product.Id,
                Title = product.Title,
                Description = product.Description,
                Price = product.Price,
                ImageUrl = product.Image == null
                ? null
                : $"/KasiConnect/Images/{product.Image}",
                CreatedAt = product.CreatedAt
            };

            return CreatedAtAction(nameof(GetProduct), new { id = product.Id }, productDto);

        }

        [HttpGet("/api/users/{userId:int}/products")]
        public async Task<IActionResult> GetUserProducts(int userId)


        {
            var userExists = await _context.Users.AnyAsync(user => user.Id == userId);

            if(!userExists)
            {
                return NotFound("User not found.");
            }

            var products = await _context.Products.Where(product => product.UserId == userId)
                           .OrderByDescending(product => product.CreatedAt).Select(product =>new ProductDto
                           {
                               Id = product.Id,
                               UserId = product.UserId,
                               Title = product.Title,
                               Description = product.Description,
                               Price = product.Price,
                               ImageUrl = product.Image == null
                               ? null
                               : $"/KasiConnect/Images/{product.Image}",
                               CreatedAt = product.CreatedAt
                           })
                           .ToListAsync();
            return Ok(products);
        }

        [Authorize]
        [HttpDelete("{id:int}")]
        public async Task<IActionResult> DeleteProduct(int id)
        {
            var userIdValue = User.FindFirstValue(ClaimTypes.NameIdentifier);
            if(!int.TryParse(userIdValue,out var userId))
            {
                return Unauthorized("Invalid Token.");
            }

            var product = await _context.Products.FindAsync(id);

            if(product == null)
            {
                return NotFound("Product not found.");
            }
            if(product.UserId != userId)
            {
                return Forbid("You can only delete your own products.");
            }

            _context.Products.Remove(product);
            await _context.SaveChangesAsync();
            
            return NoContent();
        
        }


    }

    
}
