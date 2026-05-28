namespace KasiConnect.Api.Models
{
    public class Product
    {
        public int Id { get; set; }
        public int? UserId { get; set; }
        public string? Title { get; set; }
        public string?  Description { get; set; }
        public decimal? Price { get; set; }
        public DateTime CreatedAt { get; set; }
        public string? Image { get; set; }
}
}
