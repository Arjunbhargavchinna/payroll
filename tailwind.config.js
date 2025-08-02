module.exports = {
    content: [
        "./**/*.php",
        "./**/*.html",
        "./public/js/**/*.js"
    ],
    theme: {
        extend: {},
    },
    plugins: [],
    safelist: [
        'bg-blue-500',
        'bg-green-500',
        'text-white',
        'hover:bg-blue-700',
        'hover:text-white',
        'border',
        'border-gray-300',
        // Add any classes you use dynamically or conditionally
    ],
}
