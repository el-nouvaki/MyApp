<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Maniacs</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
	<link rel="stylesheet" href="frontend/assets/css/list_cards.css">
    <style>
        body { background-color: #fff; }
        
        /* Custom CSS για το Navbar */
        .navbar-custom {
            background-color: #000000;
            height: 80px;
        }
        
        .navbar-brand img {
            height: 60px;
        }

        /* Styling για τα Links του μενού */
        .nav-link {
            color: #fff !important;
            font-size: 1.1rem;
            font-weight: 500;
            
            /* --- ΑΛΛΑΓΕΣ ΓΙΑ ΤΟ BUTTON EFFECT --- */
            
            /* 1. Padding: Δημιουργούμε χώρο γύρω από το κείμενο */
            padding: 8px 18px !important; 
            
            /* 2. Margin: Λίγο κενό ανάμεσα στα κουμπιά */
            margin: 0 5px;

            /* 3. Border Radius: Στρογγυλεμένες γωνίες (20px για οβάλ, 5px για ελαφρώς στρογγυλεμένο) */
            border-radius: 20px; 
            
            /* 4. Transition: Ομαλή εναλλαγή για όλα τα properties (χρώμα και background) */
            transition: all 0.3s ease; 
        }
        
        /* --- HOVER STATE --- */
        .nav-link:hover {
            /* Το χρώμα των γραμμάτων παραμένει λευκό για αντίθεση */
            color: #fff !important; 
            
            /* Το background γίνεται σκούρο κόκκινο */
            background-color: #b91d2b; 
            
            /* Προαιρετικά: Μια διακριτική σκιά για βάθος */
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            
            /* Προαιρετικά: Ελαφριά μετακίνηση προς τα πάνω για 3D εφέ */
            transform: translateY(-2px);
        }

        /* Styling για το Search Bar */
        .search-input { border-radius: 0; border: none; }
		
		/* Star Rating CSS */
		.rate {
			display: flex;
			flex-direction: row-reverse; /* Αντίστροφη σειρά για να δουλεύει το hover σωστά */
			justify-content: flex-end;
			height: 46px;
			padding: 0;
		}
		.rate:not(:checked) > input {
			position: absolute;
			top: -9999px;
		}
		.rate:not(:checked) > label {
			float: right;
			width: 1em;
			overflow: hidden;
			white-space: nowrap;
			cursor: pointer;
			font-size: 30px;
			color: #ccc;
		}
		.rate:not(:checked) > label:before {
			content: '★ ';
		}
		.rate > input:checked ~ label {
			color: #ffc700; /* Κίτρινο χρώμα για τα επιλεγμένα */
		}
		.rate:not(:checked) > label:hover,
		.rate:not(:checked) > label:hover ~ label {
			color: #deb217; /* Σκούρο κίτρινο στο hover */
		}
		.rate > input:checked + label:hover,
		.rate > input:checked + label:hover ~ label,
		.rate > input:checked ~ label:hover,
		.rate > input:checked ~ label:hover ~ label,
		.rate > label:hover ~ input:checked ~ label {
			color: #c59b08; /* Ακόμα πιο σκούρο όταν κάνεις hover σε ήδη επιλεγμένο */
		}
    </style>
</head>
<body>