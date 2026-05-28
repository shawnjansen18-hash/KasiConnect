namespace KasiConnect.Api.Models
{
    public class Review
    {
        public int Id { get; set; }
        public int ProductId {  get; set; }
        public int UserId { get; set; }
        public int Rating { get; set; }
        public string ReviewText { get; set; }
        public DateTime CreatedAt { get; set; }
    }
}
