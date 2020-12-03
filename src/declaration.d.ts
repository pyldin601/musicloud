declare module '*.html' {
  const src: string
  export default src
}

declare module '*.module.less' {
  const classes: { [key: string]: string }
  export default classes
}

declare module '*.module.css' {
  const classes: { [key: string]: string }
  export default classes
}
