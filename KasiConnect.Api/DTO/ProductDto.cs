namespace KasiConnect.Api.DTO
{
    public class ProductDto
    {
        public int Id { get; set; }
        
        public int? UserId { get; set; }
        public string? Title { get; set; }
        public string? Description { get; set; }
        public decimal? Price { get; set; }
        public string? ImageUrl { get; set; }
        public DateTime CreatedAt { get; set; }
        
}
}
