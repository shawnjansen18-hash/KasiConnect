namespace KasiConnect.Api.DTO
{
    public class ReviewDto
    {
        public int Id { get; set; }
        public int ProductId {  get; set; }
        public int UserId { get; set; }
        public string? UserName {  get; set; }
        public int Rating { get; set; }
        public string ReviewText { get; set; } = string.Empty;
        public DateTime CreatedAt { get; set; }
    }
}
