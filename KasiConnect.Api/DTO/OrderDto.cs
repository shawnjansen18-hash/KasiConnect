namespace KasiConnect.Api.DTO
{
    public class OrderDto
    {
        public int Id { get; set; }
        public int? ProductId { get; set;}
        public string? ProductTitle {  get; set; }
        public int? BuyerId { get; set; }
        public string? BuyerName { get; set; }
        public int? SellerId {get; set;}
        public string? Status { get; set; }
        public DateTime CreatedAt {  get; set; }
    }
}
