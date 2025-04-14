// Show specific section
function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.remove('active');
    });
    document.getElementById(sectionId).classList.add('active');
    document.querySelector(`a[onclick="showSection('${sectionId}')"]`).classList.add('active');
}

// Show Add Product Form
function showAddProductForm() {
    document.getElementById('add-product-form').classList.add('active');
    document.getElementById('form-error').style.display = 'none'; // Reset error message
}

// Hide Add Product Form
function hideAddProductForm() {
    document.getElementById('add-product-form').classList.remove('active');
    document.getElementById('form-error').style.display = 'none';
}

// Fetch Products
function fetchProducts() {
    fetch('backend/get_products.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch products: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';
            data.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.price}</td>
                    <td>${product.quantity}</td>
                    <td>${product.image ? `<img src="${product.image}" alt="${product.name}" width="50">` : 'No Image'}</td>
                    <td>${product.category}</td>
                    <td>
                        <button onclick="editProduct(${product.id})">Edit</button>
                        <button onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                `;
                productList.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            alert('Error fetching products: ' + error.message);
        });
}

// Add Product
document.getElementById('product-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitButton = this.querySelector('button[type="submit"]');
    const errorDiv = document.getElementById('form-error');
    errorDiv.style.display = 'none'; // Reset error message
    submitButton.disabled = true; // Disable button while processing
    submitButton.textContent = 'Adding...'; // Show loading state

    const formData = new FormData(this);
    fetch('backend/add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Product added successfully!');
            this.reset(); // Reset form
            hideAddProductForm();
            fetchProducts();
        } else {
            errorDiv.textContent = data.message || 'Error adding product.';
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        errorDiv.textContent = 'Error adding product: ' + error.message;
        errorDiv.style.display = 'block';
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Add Product'; // Reset button text
    });
});

// Delete Product
function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return; // Cancel if user clicks "Cancel"
    }

    const deleteButton = event.target;
    deleteButton.disabled = true; // Disable button while processing
    deleteButton.textContent = 'Deleting...'; // Show loading state

    const formData = new FormData();
    formData.append('id', id);

    fetch('backend/delete_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Delete response:', data); // Log the response for debugging
        if (data.success) {
            alert('Product deleted successfully!');
            fetchProducts(); // Refresh the product list
        } else {
            throw new Error(data.message || 'Unknown error occurred while deleting product.');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('Error deleting product: ' + error.message);
    })
    .finally(() => {
        deleteButton.disabled = false;
        deleteButton.textContent = 'Delete'; // Reset button text
    });
}

// Placeholder function for edit (to be implemented)
function editProduct(id) {
    alert('Edit product with ID: ' + id);
}

// Initial Fetch
document.addEventListener('DOMContentLoaded', fetchProducts);