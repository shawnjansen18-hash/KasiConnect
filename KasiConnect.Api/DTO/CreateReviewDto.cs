using System.ComponentModel.DataAnnotations;

namespace KasiConnect.Api.DTO
{
    public class CreateReviewDto
    {
        [Range(1,5)]
        public int Rating { get; set; }
        [Required]
        [MaxLength(1000)]
        public String ReviewText { get; set; } = string.Empty;
    }
}
