using KasiConnect.Api.Data;
using KasiConnect.Api.DTO;
using KasiConnect.Api.Models;
using Microsoft.AspNetCore.Http.HttpResults;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using System.Security.Cryptography.X509Certificates;
using Microsoft.AspNetCore.Authorization;
using System.Security.Claims;


namespace KasiConnect.Api.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class OrdersController : ControllerBase
    {
        private readonly KasiConnectDbContext _context;

        public OrdersController(KasiConnectDbContext context)
        {
            _context = context;
        }

        [Authorize]
        [HttpPost]
        public async Task<IActionResult> CreateOrder(CreateOrderDto createOrderDto)
        {
            var buyerIdValue = User.FindFirstValue(ClaimTypes.NameIdentifier);
            if(!int.TryParse(buyerIdValue, out var buyerId))
            {
                return Unauthorized("Invalid token.");
            }

            var product = await _context.Products.FirstOrDefaultAsync(product => product.Id == createOrderDto.ProductId);

            if (product == null)
            {
                return NotFound("Product not found.");
            }

            var buyerExists = await _context.Users.AnyAsync(user => user.Id == buyerId);

            if (!buyerExists)
            {
                return BadRequest("Buyer does not exist");
            }

            if(product.UserId == buyerId)
            {
                return BadRequest("You cannot buy your own product.");
            }

            var order = new Order
            {
                ProductId = product.Id,
                BuyerId = buyerId,
                SellerId = product.UserId,
                Status = "Pending"
            };

            _context.Orders.Add(order);
            await _context.SaveChangesAsync();

            var orderDto = new OrderDto
            {
                Id = order.Id,
                ProductId = order.ProductId,
                ProductTitle = product.Title,
                BuyerId = order.BuyerId,
                SellerId = order.SellerId,
                Status = order.Status,
                CreatedAt = order.CreatedAt
            };

            return CreatedAtAction(nameof(GetOrder), new { id = order.Id }, orderDto);

        }

        [HttpGet("{id:int}")]
        public async Task<IActionResult> GetOrder(int id)
        {
            var order = await _context.Orders.Where(order => order.Id == id).Select(order => new OrderDto
            {
                Id = order.Id,
                ProductId = order.ProductId,
                ProductTitle = _context.Products.Where(product => product.Id == order.ProductId)
                            .Select(product => product.Title).FirstOrDefault(),
                BuyerId = order.BuyerId,
                SellerId = order.SellerId,
                Status = order.Status,
                CreatedAt = order.CreatedAt
            })
                .FirstOrDefaultAsync();

            if (order == null)
            {
                return NotFound();
            }
            return Ok(order);
        }

        [HttpGet("/api/sellers/{sellerId:int}/orders")]
        public async Task<IActionResult> GetSellerOrders(int sellerId)
        {
            var sellerExists = await _context.Users.AnyAsync(user => user.Id == sellerId);

            if (!sellerExists)
            {
                return NotFound("Seller not found.");
            }

            var orders = await _context.Orders.Where(order => order.SellerId == sellerId)
                        .OrderByDescending(order => order.CreatedAt).ToListAsync();

            var orderDtos = new List<OrderDto>();
            foreach(var order in orders)
            {
                var productTitle = await _context.Products.Where(product => product.Id == order.ProductId)
                                   .Select(product => product.Title).FirstOrDefaultAsync();
                var buyerName = await _context.Users.Where(user => user.Id == order.BuyerId)
                                .Select(user => user.Name).FirstOrDefaultAsync();

                orderDtos.Add(new OrderDto
                {
                    Id = order.Id,
                    ProductId = order.ProductId,
                    ProductTitle = productTitle,
                    BuyerId = order.BuyerId,
                    BuyerName = buyerName,
                    SellerId = order.SellerId,
                    Status = order.Status,
                    CreatedAt = order.CreatedAt
                });
            }
                                
            return Ok(orderDtos);
        }

        [Authorize]
        [HttpPatch("{id:int}/status")]
        public async Task<IActionResult> UpdateOrderStatus(int id, UpdateOrderStatusDto updateOrderStatusDto)
        {
            var sellerIdValue = User.FindFirstValue(ClaimTypes.NameIdentifier);

            if (!int.TryParse(sellerIdValue, out var sellerId))
            {
                return Unauthorized("Invalid Token");
            }

            var allowedStatuses = new[] { "Pending", "Approved", "Completed", "Cancelled" };

            if (!allowedStatuses.Contains(updateOrderStatusDto.Status))
            {
                return BadRequest("Invalid status. Use Pending, approved, completed, or cancelled");
            }

            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound("Order not found");
            }

            order.Status = updateOrderStatusDto.Status;

            await _context.SaveChangesAsync();
            
            var productTitle = await _context.Products.Where(product => product.Id ==order.ProductId)
                               .Select(Product => Product.Title).FirstOrDefaultAsync();    

            var orderDto = new OrderDto
            {
                Id = order.Id,
                ProductId = order.ProductId,
                ProductTitle = _context.Products.Where(product => product.Id == order.ProductId)
                            .Select(product => product.Title).FirstOrDefault(),
                BuyerId = order.BuyerId,
                SellerId = order.SellerId,
                Status = order.Status,
                CreatedAt = order.CreatedAt
            };

            return Ok(orderDto);

        }

        [HttpGet("/api/users/{userId:int}/orders")]
        public async Task<IActionResult> GetUserOrders(int userId)
        {
            var userExists = await _context.Users.AnyAsync(user => user.Id == userId);

            if(!userExists)
            {
                return NotFound("User not found.");
            }

            var orders = await _context.Orders.Where(order => order.BuyerId == userId)
                        .OrderByDescending(order => order.CreatedAt).Select(order => new OrderDto
                        {
                            Id = order.Id,
                            ProductId = order.ProductId,
                            ProductTitle = _context.Products.Where(product => product.Id == order.ProductId)
                                           .Select(product => product.Title).FirstOrDefault(),
                            BuyerId = order.BuyerId,
                            SellerId = order.SellerId,
                            Status = order.Status,
                            CreatedAt = order.CreatedAt
                        })
                        .ToListAsync();
           return Ok(orders);
        }
    } 
}
