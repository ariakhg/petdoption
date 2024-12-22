<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Center</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    :root {
        --primary-blue: #1a365d;
        --light-blue: #e6f3ff;
        --yellow: #ffd233;
        --gray: #718096;
    }

    /* * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: #f7fafc;
    } */


    .back-link {
        color: #103559;
        font-size: 24px;
        display: inline-block;
        margin-bottom: 1rem;
        font-weight: 700;
        margin-left: 130px;
        margin-top: 30px;
        margin-bottom: 40px;
        text-decoration: none;
    }

    .profile-section {
        background-color: #F8F8F8;
        width: 1003px;
        height: 289px;
        padding: 73px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 40px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-image {
        width: 143px;
        height: 143px;
        border-radius: 50%;
        background-color: var(--yellow);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-image img{
        width: 143px;
        height: 143px;
        border-radius: 50%;
    }

    .profile-details h1{
        font-size: 36px;
        font-weight: 700;
        color: #103559;
        margin-top: -15px;
        margin-bottom: 30px;
    }
    .profile-details h2{
        font-size: 24px;
        font-weight: 700;
        color: #103559;
    }

    .profile-details p{
        font-size: 20px;
        font-weight: 400;
        color: #103559;
        margin-top: 10px;
    }

    .profile-right {
        margin-left: auto;
        align-items: right;
        text-align: right;

    }

    .contact-button {
        background-color: #FBD157;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        border: 1px solid #E7BD43;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        color: #1B141F;
        margin-bottom: 20px;
    }

    .findMore-button {
        background-color: #FBD157;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        border: 1px solid #E7BD43;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        color: #1B141F;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    .section-title h1{
        font-size: 36px;
        font-weight: 700;
        color: #103559;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    .listings-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        padding: 2rem;
        text-align: center;
    }

    .pet-card {
        background-color: var(--light-blue);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }

    .pet-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        object-fit: cover;
    }

    .status-tag {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }

    .available {
        background-color: #c6f6d5;
        color: #22543d;
    }

    .reserved {
        background-color: #fed7d7;
        color: #822727;
    }

    
    .review-card {
        background-color: white;
        border-radius: 0.5rem;
    }

    .view-more-button {
        display: block;
        margin: 2rem auto;
        padding: 0.5rem 2rem;
        background-color: #fff;
        border: 1px solid var(--gray);
        border-radius: 2rem;
        cursor: pointer;
    }

    .review-section {
        background-color:rgb(255, 255, 255);
        width: 1003px;
        height: 250px;
        padding: 2rem;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 40px;
        border-bottom: 1px solid #B4ABABA8;
        display: flex;
        align-items: center;
        gap: 3rem;
    }
    
    .review-image {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background-color: var(--yellow);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 120px;
    }
    
    .review-image img{
        width: 86px;
        height: 86px;
        border-radius: 50%; 
    }
    
    .review-details h3{
        font-size: 24px;
        font-weight: 700;
        color: #103559;
        /* margin-top: -15px;
        margin-bottom: 30px; */
    }
    .review-details p{
        font-size: 20px;
        font-weight: 400;
        color: #9D9C9C;
    }

    .review-details strong{
        font-size: 20px;
        font-weight: 700;
        color: #9D9C9C;
    }
    

    footer {
        background-color: #fff3e0;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
    }
</style>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php';?>

    <a href="#" class="back-link">< Adoption Center</a>

    <div class="profile-section">
        <div class="profile-image">
            <img src="images/lister.png" alt="PAWS" height="60">
        </div>
        <div class="profile-details">
            <h1>PAWS</h1>
            <h2>Adoption Center</h2>
            <p>Subang Jaya, Selangor</p>
        </div>
        <div class="profile-right">
            <button class="contact-button">Contact Lister</button>
            <div  class="profile-details">
                <p>Total Pet Listings: 8</p>
                <p>Average Rating: 4/5</p>
            </div>
        </div>
    </div>

    <div class="section-title">
        <h1>All Pet Listings</h1>
    </div>

    <div class="listings-grid">
        <!-- Pet cards will be dynamically generated here -->
    </div>

    <button class="view-more-button">View more</button>

    <div class="reviews-section">
        <div class="section-title">
            <h1>Reviews</h1>
        </div>
        <!-- Review cards will be dynamically generated here -->
    </div>

    <script>
        // Sample data
        const pets = [
            {
                name: 'Mochi',
                age: '1 Year',
                gender: 'Male',
                breed: 'Pitbull',
                location: 'Selangor',
                center: 'Paws',
                status: 'Available',
                image: 'images/dog1.jpg'
            },
            {
                name: 'Ming Ming',
                age: '2 Year',
                gender: 'Female',
                breed: 'Tabby',
                location: 'Seremban',
                center: 'Paws',
                status: 'Reserved',
                image: 'images/cat1.jpg'
            },
            {
                name: 'Selena',
                age: '8 Years',
                gender: 'Male',
                breed: 'Grey Parrot',
                location: 'Selangor',
                center: 'Paws',
                status: 'Available',
                image: 'images/bird1.png'
            }
        ];

        const reviews = [
            {   
                image: 'images/lister.png',
                name: 'Jozelle',
                rating: '4/5',
                date: 'November 30, 2024',
                review: 'Adopting from this lister was a fantastic experience! They were responsive, transparent, and genuinely cared about the pet\'s well-being. My new kitten, Luna, has been an absolute joy.',
                petAdopted: 'Luna, Domestic Shorthair Cat'
            },
            {   
                image: 'images/lister.png',
                name: 'Belle',
                rating: '4/5',
                date: 'November 29, 2024',
                review: 'Great experience adopting from PAWS',
                petAdopted: 'Max, Golden Retriever'
            }
        ];

        // Function to generate pet cards
        function generatePetCards() {
            const listingsGrid = document.querySelector('.listings-grid');
            pets.forEach(pet => {
                const card = document.createElement('div');
                card.className = 'pet-card';
                card.innerHTML = `
                    <span class="status-tag ${pet.status.toLowerCase()}">${pet.status}!</span> <br>
                    <img src="${pet.image}" alt="${pet.name}" class="pet-image">
                    <div>
                        <h3>🐾 ${pet.name}</h3>
                        <p>${pet.age}, ${pet.gender}, ${pet.breed}</p>
                        <p>📍 ${pet.location}</p>
                        <p>🏠 ${pet.center}</p>
                    </div>
                    <button class="findMore-button">Find out more →</button>
                `;
                listingsGrid.appendChild(card);
            });
        }

        // Function to generate review cards
        function generateReviewCards() {
            const reviewsSection = document.querySelector('.reviews-section');
            reviews.forEach(review => {
                const card = document.createElement('div');
                card.className = 'review-card';
                card.innerHTML = `
                    <div class="review-section">
                        <div class="review-image">
                            <img src="${review.image}" alt="${review.name}">
                        </div>
                        <div class="review-details">
                            <h3>${review.name}</h3>
                            <p><strong>Rating:</strong> ${review.rating}</p>
                            <p><strong>Date:</strong> ${review.date}</p>
                            <p><strong>Review:</strong> "${review.review}"</p>
                            <p><strong>Pet Adopted:</strong> ${review.petAdopted}</p>
                        </div>
                    </div>
                `;
                reviewsSection.appendChild(card);
            });
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            generatePetCards();
            generateReviewCards();
        });
    </script>
        <!-- Footer -->
        <footer>
        <div class="footer">
            <p>&copy;Copyright 2024 Pedoption. All rights reserved.</p>
            <img src="assets/logo.png" alt="Petdoption Logo" class="footer-logo">
            <div>
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>