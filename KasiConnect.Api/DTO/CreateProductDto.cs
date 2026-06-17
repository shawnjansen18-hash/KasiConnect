using System.ComponentModel.DataAnnotations;

namespace KasiConnect.Api.DTO
{
    public class CreateProductDto
    {
        [Required]
        [MaxLength (100)]
        public string Title {  get; set; }
        
        [Required]
        [MaxLength (3000)]
        public string Description { get; set; }
        
        [Required]
        [Range (0.01, 999999.99)]
        public decimal Price { get; set; }

        public IFormFile ImageFile { get; set; } = null;
    }
}
