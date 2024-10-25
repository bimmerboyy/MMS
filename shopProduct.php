<?php

// Define a custom exception class for file not found
class FileNotFoundException extends Exception
{
    // Constructor to initialize the exception message
    public function __construct($message = "File not found", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    // Custom string representation of the exception
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

// Define the Chargable interface
interface Chargable
{
    public function getPrice(): float;
}

// Trait PriceUtility
trait PriceUtility
{
    public function getPriceAsNumber(): float
    {
        return $this->getPrice();
    }
}

// ShopProduct class implementing Chargable interface
class ShopProduct implements Chargable
{
    use PriceUtility; 

    public function __construct(
        private string $title = 'title',
        private string $name = 'FirstName',
        private string $surname = 'Surname',
        private float $price = 0.0
    ) {
    }

    public function getTitle(): string { return $this->title; }
    public function getName(): string { return $this->name; }
    public function getSurname(): string { return $this->surname; }

    public function getPrice(): float { return $this->price; }

    public function getProducer(): string
    {
        return $this->name . ' ' . $this->surname;
    }
}

// Service class implementing Chargable interface
class Service implements Chargable
{
    use PriceUtility;

    public function __construct(
        private string $serviceName,
        private float $serviceCost
    ) {
    }

    public function getServiceName(): string { return $this->serviceName; }
    public function getPrice(): float { return $this->serviceCost; }
}

// UtilityService class using PriceUtility trait
class UtilityService implements Chargable
{
    use PriceUtility;

    public function __construct(
        private string $utilityName,
        private float $utilityCost
    ) {
    }

    public function getUtilityName(): string { return $this->utilityName; }
    public function getPrice(): float { return $this->utilityCost; }
}

// ShopProductWriter class
class ShopProductWriter
{
    protected array $products = [];

    public function addProduct(Chargable $product): void
    {
        $this->products[] = $product;
    }

    public function write(): string
    {
        $output = '';

        foreach ($this->products as $product) {
            if ($product instanceof ShopProduct) {
                $output .= $product->getTitle() . " - Produced by: " . $product->getProducer() . " (Price: $" . number_format($product->getPrice(), 2) . ")\n";
            } elseif ($product instanceof Service) {
                $output .= $product->getServiceName() . " - Service Cost: $" . number_format($product->getPrice(), 2) . "\n";
            } elseif ($product instanceof UtilityService) {
                $output .= $product->getUtilityName() . " - Utility Cost: $" . number_format($product->getPrice(), 2) . "\n";
            }
        }

        return $output;
    }
}

// XMLProductWriter class inheriting from ShopProductWriter
class XMLProductWriter extends ShopProductWriter
{
    public function write(): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $productsElement = $dom->createElement('products');
        $dom->appendChild($productsElement);

        foreach ($this->products as $product) {
            $productElement = $dom->createElement('product');
            $productsElement->appendChild($productElement);

            if ($product instanceof ShopProduct) {
                $titleElement = $dom->createElement('title', $product->getTitle());
                $productElement->appendChild($titleElement);

                $producerElement = $dom->createElement('producer');
                $nameElement = $dom->createElement('name', $product->getName());
                $surnameElement = $dom->createElement('surname', $product->getSurname());
                $producerElement->appendChild($nameElement);
                $producerElement->appendChild($surnameElement);
                $productElement->appendChild($producerElement);

                $priceElement = $dom->createElement('price', number_format($product->getPrice(), 2));
                $productElement->appendChild($priceElement);
            } elseif ($product instanceof Service) {
                $serviceNameElement = $dom->createElement('serviceName', $product->getServiceName());
                $productElement->appendChild($serviceNameElement);

                $serviceCostElement = $dom->createElement('serviceCost', number_format($product->getPrice(), 2));
                $productElement->appendChild($serviceCostElement);
            } elseif ($product instanceof UtilityService) {
                $utilityNameElement = $dom->createElement('utilityName', $product->getUtilityName());
                $productElement->appendChild($utilityNameElement);

                $utilityCostElement = $dom->createElement('utilityCost', number_format($product->getPrice(), 2));
                $productElement->appendChild($utilityCostElement);
            }
        }

        return $dom->saveXML();
    }

    public function saveToFile(string $fileName): void
    {
        $xmlContent = $this->write();
        file_put_contents($fileName, $xmlContent);
    }

    // Method to load from file with file existence check
    public function loadFromFile(string $fileName): string
    {
        // Check if the file exists, if not throw FileNotFoundException
        if (!file_exists($fileName)) {
            throw new FileNotFoundException("The XML file '$fileName' does not exist.");
        }

        // Load and return the file contents if it exists
        return file_get_contents($fileName);
    }
}

// Example usage of XMLProductWriter and FileNotFoundException
try {
    $xmlWriter = new XMLProductWriter();

    // Add products to XML writer
    $cd1 = new ShopProduct("Thriller", "Michael", "Jackson", 12.99);
    $xmlWriter->addProduct($cd1);

    // Save XML to a file
    $xmlWriter->saveToFile('products.xml');

    // Attempt to load a non-existent file
    $xmlWriter->loadFromFile('nonexistent.xml'); // This will throw FileNotFoundException
} catch (FileNotFoundException $e) {
    echo $e; // Output the exception message
}

final class Checkout
{
    private array $items = [];
    private float $total = 0.0;
    private CalculateTax $taxCalculator;

    public function __construct(CalculateTax $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    // Add a product to the checkout list
    public function addItem(Chargable $item): void
    {
        $this->items[] = $item;
    }

    // Calculate total cost including tax
    public function calculateTotal(): float
    {
        foreach ($this->items as $item) {
            // Add item price
            $this->total += $item->getPrice();
            // Add tax
            $this->total += $this->taxCalculator->calculateTax($item);
        }

        return $this->total;
    }

    // Get total before tax
    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += $item->getPrice();
        }
        return $subtotal;
    }

    // Get total tax amount
    public function getTotalTax(): float
    {
        $totalTax = 0.0;
        foreach ($this->items as $item) {
            $totalTax += $this->taxCalculator->calculateTax($item);
        }
        return $totalTax;
    }
}

// Example usage:

// Instantiate a tax calculator with a 20% tax rate
$taxCalculator = new CalculateTax(0.2);

// Create a checkout instance
$checkout = new Checkout($taxCalculator);

// Add items (products, services, utilities)
$checkout->addItem(new ShopProduct("Thriller", "Michael", "Jackson", 12.99));
$checkout->addItem(new Service("Web Hosting", 50.00));
$checkout->addItem(new UtilityService("Electricity", 100.00));

// Calculate the subtotal, tax, and total
echo "Subtotal: $" . number_format($checkout->getSubtotal(), 2) . "<br>";
echo "Total Tax: $" . number_format($checkout->getTotalTax(), 2) . "<br>";
echo "Total Checkout: $" . number_format($checkout->calculateTotal(), 2) . "<br>";



?>
