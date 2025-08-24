<?php
session_start();
// The session check has been removed to allow anonymous access.

$submitted = isset($_GET['success']) && $_GET['success'] == "1";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Found Pet | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(-45deg, #f5f7fa, #c3cfe2, #e8eaff, #4353ff);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card-container {
            width: 100%;
            max-width: 650px;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out;
            transition: transform 0.3s ease-in-out;
        }

        .card-container:hover {
            transform: scale(1.02);
        }

        .form-label {
            font-weight: 700;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #4353ff;
            box-shadow: 0 0 0 4px rgba(67, 83, 255, 0.25);
            transform: scale(1.01);
        }

        .btn-custom {
            background-color: #4353ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #3545e0;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 83, 255, 0.3);
        }

        #map {
            height: 250px;
            border-radius: 12px;
            margin-top: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .image-preview {
            height: 100px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            margin: 0.25rem;
            transition: transform 0.3s ease-in-out;
        }

        .image-preview:hover {
            transform: scale(1.05);
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<main class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card-container text-center">
        <h3 class="mb-4">🐾 Report a Found Pet</h3>
        <p class="text-muted mb-4">Provide details to help reunite a pet with its owner.</p>

        <form action="report_found_process.php" method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="pet_name" class="form-label">Pet Name (if known)</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" placeholder="E.g., Buddy">
                </div>
                <div class="col-md-6">
                    <label for="breed" class="form-label">Breed</label>
                    <input type="text" class="form-control" id="breed" name="breed" placeholder="E.g., Golden Retriever">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="photos" class="form-label">Upload Photos</label>
                <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*" multiple required onchange="previewImages()">
                <div id="preview" class="mt-3 d-flex flex-wrap gap-2 justify-content-center"></div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Describe the Pet</label>
                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Color, behavior, any identifying marks, etc." required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="last_seen_date" class="form-label">Date Found</label>
                <input type="date" class="form-control" id="last_seen_date" name="last_seen_date" required>
            </div>
            
            <div class="mb-4">
                <div class="form-check text-start">
                    <input class="form-check-input" type="checkbox" id="shareLocationCheck" checked>
                    <label class="form-check-label" for="shareLocationCheck">
                        Share my current location
                    </label>
                </div>
                <div id="map" class="mt-3" style="display: none;"></div>
                <input type="hidden" name="last_seen_location" id="last_seen_location">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-custom px-4 py-2">Submit Report</button>
            </div>
        </form>
    </div>
</main>

<script>
    // This function now handles the new optional location logic
    function submitForm() {
        const form = document.querySelector('form');
        const shareLocation = document.getElementById('shareLocationCheck').checked;

        if (shareLocation && navigator.geolocation) {
            Swal.fire({
                title: 'Getting your location...',
                text: 'Please wait while we pinpoint the location where you found the pet.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;
                    
                    // Reverse geocoding to get human-readable address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            const address = data.display_name || "Unknown Location";
                            document.getElementById("last_seen_location").value = address;
                            form.submit();
                        })
                        .catch(error => {
                            console.error('Reverse Geocoding Error:', error);
                            document.getElementById("last_seen_location").value = "Unknown Location";
                            form.submit();
                        });
                }, 
                function(error) {
                    console.error("Geolocation error:", error);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Location Not Shared',
                        text: 'Your report will be submitted without location data. You can add it later if you wish.',
                        confirmButtonColor: '#4353ff'
                    }).then(() => {
                        document.getElementById("last_seen_location").value = "Location not shared";
                        document.getElementById("latitude").value = "";
                        document.getElementById("longitude").value = "";
                        form.submit();
                    });
                }, 
                {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        } else {
            // If location sharing is not checked or not supported, submit the form immediately
            document.getElementById("last_seen_location").value = "Location not shared";
            document.getElementById("latitude").value = "";
            document.getElementById("longitude").value = "";
            form.submit();
        }
        return false; // Prevent form from submitting normally
    }
    
    function previewImages() {
        const preview = document.getElementById("preview");
        const files = document.getElementById("photos").files;
        preview.innerHTML = "";

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement("img");
                img.src = e.target.result;
                img.classList.add("image-preview");
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    <?php if ($submitted): ?>
    Swal.fire({
        icon: 'success',
        title: 'Report Submitted!',
        text: 'Thank you for helping reunite pets with their owners 🎉',
        confirmButtonColor: '#4353ff'
    });
    <?php endif; ?>
</script>

</body>
</html>
