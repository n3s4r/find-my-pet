<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Pet Services | Find My Pet</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_Maps_API_KEY&libraries=places&callback=initMap" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 70px; /* Space for fixed navbar */
            padding-bottom: 2rem;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }

        main.container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .map-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            padding: 20px;
            animation: fadeInUp 0.8s ease-out;
        }
        
        #map {
            flex-grow: 1;
            border-radius: 15px;
            min-height: 65vh;
        }

        .page-title {
            color: #343a40;
            font-weight: 700;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">🐾 Find My Pet</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <div class="text-center mb-4">
        <h3 class="page-title">🗺️ Nearby Vets & Pet Shops</h3>
        <p class="text-muted">The map below shows services within a 5km radius of your location.</p>
    </div>
    <div class="map-container">
        <div id="map"></div>
    </div>
</main>

<script>
    // This function is called by the Google Maps script when it has loaded.
    function initMap() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                const map = new google.maps.Map(document.getElementById("map"), {
                    center: userLocation,
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                });

                // Create a custom marker for the user's location
                new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "You are here",
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: "#4353ff",
                        fillOpacity: 1,
                        strokeColor: "white",
                        strokeWeight: 3
                    }
                });
                
                // Create a single InfoWindow to be reused for all places
                const infoWindow = new google.maps.InfoWindow();
                
                // Function to perform search and create markers
                const performSearch = (searchType, iconUrl) => {
                    const service = new google.maps.places.PlacesService(map);
                    const request = {
                        location: userLocation,
                        radius: 5000, // 5km radius
                        types: [searchType] // Use `types` array
                    };

                    service.nearbySearch(request, (results, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK && results) {
                            results.forEach(place => {
                                const marker = new google.maps.Marker({
                                    map,
                                    position: place.geometry.location,
                                    title: place.name,
                                    icon: {
                                        url: iconUrl,
                                        scaledSize: new google.maps.Size(40, 40)
                                    }
                                });

                                // Add a click listener to each marker to show an InfoWindow
                                marker.addListener('click', () => {
                                    const content = `
                                        <strong>${place.name}</strong><br>
                                        ${place.vicinity}
                                    `;
                                    infoWindow.setContent(content);
                                    infoWindow.open(map, marker);
                                });
                            });
                        }
                    });
                };
                
                // Perform two separate searches: one for vets, one for pet stores
                performSearch('veterinary_care', 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'); // Vet icon
                performSearch('pet_store', 'https://maps.google.com/mapfiles/ms/icons/purple-dot.png');      // Pet Store icon

            }, () => {
                // Handle case where user denies location access
                alert("Location access is required to find nearby services. Please allow location access and refresh the page.");
                document.getElementById('map').innerHTML = '<div class="alert alert-warning h-100 d-flex align-items-center justify-content-center">Location access denied.</div>';
            });
        } else {
            // Handle case where browser doesn't support geolocation
            alert("Geolocation is not supported by your browser.");
             document.getElementById('map').innerHTML = '<div class="alert alert-danger h-100 d-flex align-items-center justify-content-center">Geolocation not supported.</div>';
        }
    }
</script>

</body>
</html>