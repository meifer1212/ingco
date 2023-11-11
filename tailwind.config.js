/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {},
      theme: {
        pagination: theme => ({
            // Customize the color only. (optional)
            color: theme('colors.teal.600'),

            // Customize styling using @apply. (optional)
            wrapper: 'flex justify-center list-reset',

            // Customize styling using CSS-in-JS. (optional)
            wrapper: {
                'display': 'flex',
                'justify-items': 'center',
                '@apply list-reset': {},
            },
        })
    },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        // pagination
        require("tailwindcss-plugins/pagination"),
    ],
  }
