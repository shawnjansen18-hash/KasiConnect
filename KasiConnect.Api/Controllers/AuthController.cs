using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using KasiConnect.Api.Data;
using KasiConnect.Api.DTO;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using KasiConnect.Api.Models;

namespace KasiConnect.Api.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class AuthController : ControllerBase
    {
        private readonly KasiConnectDbContext _context;
        private readonly IConfiguration _configuration;

        public AuthController(KasiConnectDbContext context, IConfiguration configuration)
        {
            _context = context;
            _configuration = configuration;
        }

        [HttpPost("login")]
        public async Task<IActionResult> Login(LoginDto loginDto)
        {
            var user = await _context.Users.FirstOrDefaultAsync(user => user.Email == loginDto.Email);

            if ( user == null || string.IsNullOrWhiteSpace(user.Password))
            {
                return Unauthorized("Invlaid email or password.");     
            }

            var passwordIsValid = BCrypt.Net.BCrypt.Verify(loginDto.Password, user.Password);
            
            if(!passwordIsValid)
            {
                return Unauthorized("Invalid email or password.");
            }

            var token = GenerateJwtToken(user.Id, user.Name, user.Email);

            var response = new AuthResponseDto
            {
                UserId = user.Id,
                Name = user.Name,
                Email = user.Email,
                Token = token
            };

            return Ok(response);               
        }

        private string GenerateJwtToken(int userId, string? name, string? email)
        {
            var jwtKey = _configuration["Jwt:Key"];
            var jwtIssuer = _configuration["Jwt:Issuer"];
            var jwtAudience = _configuration["Jwt:Audience"];
            var expiresInMinutes = Convert.ToDouble(_configuration["Jwt:ExpiresInMinutes"]);

            var claims = new List<Claim>
            {
                new Claim(ClaimTypes.NameIdentifier, userId.ToString()),
                new Claim(ClaimTypes.Name, name ?? string.Empty),
                new Claim(ClaimTypes.Email, email ?? string.Empty)
            };

            var key = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(jwtKey!));
            var credentials = new SigningCredentials(key, SecurityAlgorithms.HmacSha256);

            var token = new JwtSecurityToken(
                issuer: jwtIssuer,
                audience: jwtAudience,
                claims: claims,
                expires: DateTime.UtcNow.AddMinutes(expiresInMinutes),
                signingCredentials: credentials
                );

            return new JwtSecurityTokenHandler().WriteToken(token);

        }

        [HttpPost("register")]
        public async Task<IActionResult> Register(RegisterDto registerDto)
        {
            var emailExists = await _context.Users.AnyAsync(user => user.Email == registerDto.Email);

            if(emailExists)
            {
                return BadRequest("Email is already registered");
            }

            var passwordHash = BCrypt.Net.BCrypt.HashPassword(registerDto.Password);

            var user = new User
            {
                Name = registerDto.Name,
                Email = registerDto.Email,
                Password = passwordHash
            };

            _context.Users.Add(user);
            await _context.SaveChangesAsync();

            var token = GenerateJwtToken(user.Id, user.Name, user.Email);

            var response = new AuthResponseDto
            {
                UserId = user.Id,
                Name = user.Name,
                Email = user.Email,
                Token = token
            };

            return CreatedAtAction(nameof(Login), response);


        }

    }
}
