export function max(items: number[]): number {
  return items.reduce((a, b) => (a > b ? a : b), 0)
}
