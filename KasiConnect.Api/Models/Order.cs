namespace KasiConnect.Api.Models
{
    public class Order
    {
        public int Id { get; set; }
        public int? ProductId {  get; set; }
        public int? BuyerId {  get; set; }
        public int? SellerId {  get; set; }
        public string? Status { get; set; }
        public DateTime CreatedAt { get; set; }
    }
}
