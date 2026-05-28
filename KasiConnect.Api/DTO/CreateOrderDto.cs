using System.ComponentModel.DataAnnotations;

namespace KasiConnect.Api.DTO
{
    public class CreateOrderDto
    {
        [Required]
        public int ProductId {  get; set; }
        [Required]
        public int BuyerId {  get; set; }
    }
}
