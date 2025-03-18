class Customers {
    List<Customer> customers;

    Customers add(Customer customer) {
        List<Customer> result = new ArrayList<>(customers);
        result.add(customer);
        return new Customers(result);
    }
}