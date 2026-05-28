using System.ComponentModel.DataAnnotations;
namespace KasiConnect.Api.DTO
{
    public class UpdateOrderStatusDto
    {
        [Required]
        public string Status { get; set; } = string.Empty;
    }
}
